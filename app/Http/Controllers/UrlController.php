<?php

namespace App\Http\Controllers;

use App\Http\Requests\UrlStoreRequest;
use App\Models\Url;
use Hashids\Hashids;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class UrlController extends Controller
{
    public function __construct(protected Hashids $hashids) {}
    
    public function store(UrlStoreRequest $request)
    {
        $longUrl = $request->input('long_url');
        $expiredAt = $request->input('expired_at');
        
        $id = Redis::incr('url:id');
        $encodedCode = $this->hashids->encode($id);

        Url::create([
            'id'         => $id,
            'long_url'   => $longUrl,
            'expired_at' => $expiredAt,
        ]);

        $ttl = $expiredAt
            ? now()->diffInSeconds(Carbon::parse($expiredAt))
            : now()->addDays(7);
Cache::put($encodedCode, $longUrl, $ttl);
\Log::info('cache put', ['code' => $encodedCode, 'url' => $longUrl, 'ttl' => $ttl]);
        Cache::put($encodedCode, $longUrl, $ttl);

        return response()->json([
            'short_url' => config('app.url') . '/' . $encodedCode,
        ]);
    }

    public function redirect(string $code)
    {
        $longUrl = Cache::get($code);

        if ($longUrl) {
            Url::where('id', $this->hashids->decode($code)[0])
                ->increment('count');
            return redirect($longUrl);
        }

        $id = $this->hashids->decode($code)[0] ?? null;

        if (!$id) {
            abort(404);
        }

        $url = Url::find($id);

        if (!$url || ($url->expired_at && $url->expired_at->isPast())) {
            abort(404);
        }

        $ttl = $url->expired_at
            ? now()->diffInSeconds($url->expired_at)
            : now()->addDays(7);

        Cache::put($code, $url->long_url, $ttl);

        $url->increment('count');

        return redirect($url->long_url);
    }
}