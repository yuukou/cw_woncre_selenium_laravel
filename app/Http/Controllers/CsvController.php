<?php
/**
 * @package App\Http\Controllers
 * @copyright Copyright (C) Logical-Studio Co.,Ltd.
 * @since 2019-07-25
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Csv
 */
class CsvController extends Controller
{
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
     *
     *
     * @param Request $request
     */
    public function post(Request $request)
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file = $request->csv_file;
        $data = file_get_contents($file);
        $data = mb_convert_encoding($data, 'UTF-8', 'sjis-win');
        $temp = tmpfile();
        $csv  = [];

        fwrite($temp, $data);
        rewind($temp);

        while (($data = fgetcsv($temp, 0, ",")) !== FALSE) {
            //headerのスキップ処理
            if ($data[0] === 'キーワード') {
                continue;
            }
            $importData = [
                'word1' => $data[1],
                'word2' => $data[2],
                'word3' => $data[3],
                'word4' => $data[4],
                'word5' => $data[5],
                'word6' => $data[6],
                'word7' => $data[7],
                'word8' => $data[8],
                'word9' => $data[9],
                'word10' => $data[10],
                'merukari' => $data[11],
                'furiru' => $data[12],
                'rakuma' => $data[13],
                'otama' => $data[14],
                'zozo' => $data[15],
                'ticket' => $data[16],
                'shopiz' => $data[17],
                'yahoo' => $data[18],
                'bukuma' => $data[19],
                'monokyun' => $data[20],
                'lower' => $data[21],
                'max' => $data[22],
                'alert' => $data[23]
            ];
            $csv[] = $importData;
        }
        fclose($temp);
    }
}