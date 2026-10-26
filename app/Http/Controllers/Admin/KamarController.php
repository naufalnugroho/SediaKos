<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Kos;
use App\Services\OnlineRentalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KamarController extends Controller
{
    public function index(Request $request): View
    {
        app(OnlineRentalService::class)->expire();
        $request->validate(['q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:tersedia,terisi,dipesan,pemeliharaan'], 'kos_id' => ['nullable', 'integer', 'exists:kos,id']]);
        $query = Kamar::with('kos')->withCount(['penyewaan as sedang_digunakan' => fn ($q) => $q->whereIn('status', ['aktif', 'menunggu_pembayaran', 'menunggu_verifikasi'])]);
        $query->when($request->filled('q'), fn ($q) => $q->where('kode', 'like', '%'.$request->input('q').'%'));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')));
        $query->when($request->filled('kos_id'), fn ($q) => $q->where('kos_id', $request->integer('kos_id')));

        return view('admin.kamar.index', ['kamar' => $query->latest()->paginate(15)->withQueryString(), 'kos' => Kos::orderBy('nama')->get()]);
    }

    public function create(): View
    {
        return view('admin.kamar.form', ['kamar' => new Kamar, 'kos' => Kos::orderBy('nama')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Kamar::create($this->validated($request));

        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar): View
    {
        return view('admin.kamar.form', ['kamar' => $kamar, 'kos' => Kos::orderBy('nama')->get()]);
    }

    public function update(Request $request, Kamar $kamar): RedirectResponse
    {
        $data = $this->validated($request, $kamar);
        DB::transaction(function () use ($kamar, $data) {
            $kamar = Kamar::lockForUpdate()->findOrFail($kamar->id);
            if ($kamar->penyewaan()->whereIn('status', ['aktif', 'menunggu_pembayaran', 'menunggu_verifikasi'])->exists()) {
                throw ValidationException::withMessages(['status' => 'Kamar sedang disewa atau dipesan; selesaikan proses penyewaan terlebih dahulu.']);
            }
            $kamar->update($data);
        });

        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar): RedirectResponse
    {
        if ($kamar->penyewaan()->exists()) {
            return back()->with('error', 'Kamar yang memiliki riwayat penyewaan tidak dapat dihapus.');
        }
        $kamar->delete();

        return back()->with('success', 'Kamar berhasil dihapus.');
    }

    private function validated(Request $request, ?Kamar $kamar = null): array
    {
        return $request->validate([
            'kos_id' => ['required', 'exists:kos,id'], 'kode' => ['required', 'string', 'max:50', Rule::unique('kamar')->where('kos_id', $request->input('kos_id'))->ignore($kamar?->id)],
            'harga' => ['required', 'numeric', 'min:0'], 'harga_berlaku_mulai' => ['nullable', 'date'],
            'harga_berlaku_sampai' => ['nullable', 'date', 'after_or_equal:harga_berlaku_mulai'],
            'status' => ['required', 'in:tersedia,terisi,pemeliharaan'],
        ]);
    }
}
