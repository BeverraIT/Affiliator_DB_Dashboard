<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCreatorRequest;
use App\Http\Requests\StoreSendSampleRequest;
use App\Models\Creator;
use App\Models\SendSample;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Pintu masuk KORA (creator agent) untuk mengisi menu Kreator dan Kirim Sampel.
 * Aturan validasinya sama dengan form dashboard; pelakunya selalu akun KORA (config kora.user_id).
 * Dijaga middleware kora.token. Idempoten: mengirim data yang sama dua kali tidak membuat baris ganda.
 */
class KoraApiController extends Controller
{
    /** Cari atau buat kreator. Kreator yang sudah ada (diinput tim) tidak pernah diubah. */
    public function kreator(Request $request): JsonResponse
    {
        $input = $request->all();
        if (isset($input['username'])) {
            $input['username'] = Creator::normalizeUsername((string) $input['username']);
        }

        // Aturan form kreator, minus "unik": kreator yang sudah ada dikembalikan apa adanya.
        $aturan = Arr::except((new StoreCreatorRequest())->rules(), ['username', 'top_creator']);
        $aturan['username'] = ['required', 'string', 'max:150', 'regex:/^[a-z0-9._]+$/'];
        // Form mewajibkan kategori; API tetap menerima tanpa kategori supaya KORA versi lama tidak tertolak.
        $aturan['categories'] = ['nullable', 'array', 'max:5'];

        $v = Validator::make($input, $aturan);
        if ($v->fails()) {
            return response()->json(['message' => 'Data kreator tidak valid.', 'errors' => $v->errors()], 422);
        }
        $data = $v->validated();

        $ada = Creator::where('username', $data['username'])->first();
        if ($ada) {
            return response()->json(['id' => $ada->id, 'created' => false, 'is_blocked' => (bool) $ada->is_blocked]);
        }

        $creator = DB::transaction(function () use ($data) {
            $baris = Arr::except($data, ['ages', 'categories', 'contentstyles']);
            $baris['user_id'] = config('kora.user_id');
            $baris['top_creator'] = 0;

            $creator = Creator::create($baris);
            $creator->ages()->sync($data['ages'] ?? []);
            $creator->categories()->sync($data['categories'] ?? []);
            $creator->contentStyles()->sync($data['contentstyles'] ?? []);

            return $creator;
        });

        return response()->json(['id' => $creator->id, 'created' => true, 'is_blocked' => false], 201);
    }

    /** Buat baris Kirim Sampel atas nama KORA. Kreator+produk+tanggal deal yang sama dikembalikan, tidak digandakan. */
    public function kirimSampel(Request $request): JsonResponse
    {
        $aturan = Arr::except((new StoreSendSampleRequest())->rules(), ['pic', 'approve']);
        $aturan['status_chat'] = ['nullable', 'integer', 'in:0,1'];
        $aturan['tanggal_dichat'] = ['nullable', 'date'];

        $v = Validator::make($request->all(), $aturan);
        if ($v->fails()) {
            return response()->json(['message' => 'Data kirim sampel tidak valid.', 'errors' => $v->errors()], 422);
        }
        $data = $v->validated();

        $ada = SendSample::where('creator_id', $data['creator_id'])
            ->where('product_id', $data['product_id'])
            ->whereDate('deal_date', $data['deal_date'])
            ->first();
        if ($ada) {
            return response()->json(['id' => $ada->id, 'created' => false]);
        }

        $kora = config('kora.user_id');
        $data['pic'] = $kora;
        $data['approve'] = $kora;
        // Sama dengan form dashboard (KirimSampelController::store): estimasi sampai = kirim + 7 hari.
        $data['estimasi_sampai'] = Carbon::parse($data['tanggal_kirim'])->addDays(7)->format('Y-m-d');
        $data['perjanjian_livestream'] = $data['perjanjian_livestream'] ?? 0;

        $baris = SendSample::create($data);

        return response()->json(['id' => $baris->id, 'created' => true], 201);
    }

    /** Lengkapi baris buatan KORA yang datanya menyusul (ID pesanan, tanggal terima, video, chat). Baris tim tidak boleh disentuh. */
    public function perbaruiKirimSampel(Request $request, SendSample $sendSample): JsonResponse
    {
        if ((int) $sendSample->pic !== (int) config('kora.user_id')) {
            return response()->json(['message' => 'Baris ini bukan buatan KORA.'], 403);
        }

        $v = Validator::make($request->all(), [
            'id_pesanan' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tanggal_aktual' => ['sometimes', 'nullable', 'date'],
            'video_dibuat' => ['sometimes', 'integer', 'min:0'],
            'status_chat' => ['sometimes', 'integer', 'in:0,1'],
            'tanggal_dichat' => ['sometimes', 'nullable', 'date'],
        ]);
        if ($v->fails()) {
            return response()->json(['message' => 'Data tidak valid.', 'errors' => $v->errors()], 422);
        }

        $sendSample->update($v->validated());

        return response()->json(['id' => $sendSample->id, 'updated' => true]);
    }
}
