<?php

namespace App\Http\Controllers;

use App\Http\Services\SeleniumService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

/**
 * Csv
 */
class CsvController extends Controller
{
    private $seleniumService;

    public function __construct(SeleniumService $seleniumService)
    {
        $this->seleniumService = $seleniumService;
    }

    /**
     * csv登録画面
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function index()
    {
        return view('csv.index');
    }

    /**
     * 自動登録用のデータを受信し、自動登録を行なう
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function post(Request $request)
    {
        if ($request->input('email') && $request->input('password') &&  $request->input('authentication')) {
            $email = $request->input('email');
            $passWord = $request->input('password');
            $authentication = $request->input('authentication');
        }

//        setlocale(LC_ALL, 'ja_JP.UTF-8');
//        $file = $request->csv_file;
//
//        // ファイルの読み込み
//        $data = file_get_contents($file);
//        // 文字コードの変換（UTF-8 → SJIS-win）
//        $data = mb_convert_encoding($data, 'UTF-8', 'SJIS-win');
//        // 一時ファイルの作成
//        $temp = tmpfile();
//        // 一時ファイル書き込み
//        fwrite($temp, $data);
//        // ファイルポインタの位置を先頭に
//        rewind($temp);
//
////        foreach ($objFile as $aryData) {
////            if ($aryData === null) {
////                continue;
////            }
////            $aryDataArray = explode(',', $aryData[0]);
////            //headerのスキップ処理
////            if ($aryDataArray[0] === 'キーワード') {
////                continue;
////            }
////            $importData = [
////                'keyword' => $aryDataArray[0],
////                'word1' => $aryDataArray[1],
////                'word2' => $aryDataArray[2],
////                'word3' => $aryDataArray[3],
////                'word4' => $aryDataArray[4],
////                'word5' => $aryDataArray[5],
////                'word6' => $aryDataArray[6],
////                'word7' => $aryDataArray[7],
////                'word8' => $aryDataArray[8],
////                'word9' => $aryDataArray[9],
////                'word10' => $aryDataArray[10],
////                'merukari' => $aryDataArray[11],
////                'furiru' => $aryDataArray[12],
////                'rakuma' => $aryDataArray[13],
////                'otama' => $aryDataArray[14],
////                'zozo' => $aryDataArray[15],
////                'ticket' => $aryDataArray[16],
////                'shopiz' => $aryDataArray[17],
////                'yahoo' => $aryDataArray[18],
////                'bukuma' => $aryDataArray[19],
////                'monokyun' => $aryDataArray[20],
////                'lower' => $aryDataArray[21],
////                'max' => $aryDataArray[22],
////                'alert' => $aryDataArray[23]
////            ];
////
////            $csv[] = $importData;
////        }
////
////        fclose($temp);
////        $objFile = null;
//
//        $csv = [];
//
//        while (($data = fgetcsv($temp, 0, ",")) !== FALSE) {
//            //headerのスキップ処理
//            if ($data[0] === 'キーワード') {
//                continue;
//            }
//
//            $importData = [
//                'keyword' => $data[0],
//                'word1' => $data[1],
//                'word2' => $data[2],
//                'word3' => $data[3],
//                'word4' => $data[4],
//                'word5' => $data[5],
//                'word6' => $data[6],
//                'word7' => $data[7],
//                'word8' => $data[8],
//                'word9' => $data[9],
//                'word10' => $data[10],
//                'merukari' => $data[11],
//                'furiru' => $data[12],
//                'rakuma' => $data[13],
//                'otama' => $data[14],
//                'zozo' => $data[15],
//                'ticket' => $data[16],
//                'shopiz' => $data[17],
//                'yahoo' => $data[18],
//                'bukuma' => $data[19],
//                'monokyun' => $data[20],
//                'lower' => $data[21],
//                'max' => $data[22],
//                'alert' => $data[23]
//            ];
//            $csv[] = $importData;
//        }
//
//        fclose($temp);

        $csv = $this->parseCsv($request->csv_file);
        $this->seleniumService->exec($email, $passWord, $authentication, $csv);
        return Redirect::route('csv::index');
    }

    private function parseCsv($file)
    {
        $str = file_get_contents($file);
        $is_win = strpos(PHP_OS, "WIN") === 0;
        // Windowsの場合は Shift_JIS、Unix系は UTF-8で処理
        if ( $is_win ) {
            setlocale(LC_ALL, "Japanese_Japan.932");
        } else {
            setlocale(LC_ALL, "ja_JP.UTF-8");
            $str = mb_convert_encoding($str, "UTF-8", "SJIS-win");
        }
        $result = array();
        $fp = fopen("php://temp", "r+");
        fwrite($fp, str_replace(array("\r\n", "\r" ), "\n", $str));
        rewind($fp);

        $index = 0;
        while($row = fgetcsv($fp)) {
            //headerのスキップ処理
            if ($index == 0) {
                $index = 1;
                continue;
            } else {
                // windows の場合はSJIS-win → UTF-8 変換
                $result[] = $is_win
                    ? array_map(function($val){return mb_convert_encoding($val, "UTF-8", "SJIS-win");}, $row)
                    : $row;
            }
        }
        fclose($fp);
        return $result;
    }
}
