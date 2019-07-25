<?php

namespace App\Http\Controllers;

use App\Facades\Twitter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TwitterController
 *
 * @package App\Http\Controllers
 */
class TwitterController extends Controller
{
    /**
     * twitterのトレンド一覧を取得
     *
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function index(Request $request)
    {
        //トレンドを取得・場所は東京・ハッシュタグを省いている
        $result = Twitter::get('trends/place', ["id" => TOKYO, "exclude" => 'hashtags'])[0]->trends;
        // 本日の日付けを取得
        $today = Carbon::today()->format('Y-m-d');
        //Viewのtwitter/index.blade.phpに渡す
        return view('twitter/index', compact('result', 'today'));
    }

    /**
     * トレンド一覧にそれぞれ紐づくtweetを取得
     *
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function detail(Request $request)
    {
        // トレンドワードを取得
        $trendWord = $request['trend'];
        if (empty($trendWord)) {
            throw new NotFoundHttpException('トレンドワードが存在しません');
        }
        $maxId = '';
        if (isset($request['max_id'])) {
            $maxId = $request['max_id'];
        }
         // エンコードされたクエリパラメータをデコード
        $trendWord = urldecode($trendWord);
        // トレンドワードに紐づくツイートを取得
        $tweetResult = collect(Twitter::get('search/tweets', ["q" => $trendWord, 'max_id' => (!empty($maxId) ? $maxId :'')])->statuses);
        $maxId = $tweetResult->last()->id;
        return view('twitter/detail', compact('trendWord', 'tweetResult', 'maxId'));
    }
}
