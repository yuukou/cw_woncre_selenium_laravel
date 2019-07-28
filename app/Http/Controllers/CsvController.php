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
                'keyword' => $data[0],
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

        $this->seleniumService->exec($email, $passWord, $authentication, $csv);
        return Redirect::route('csv::index');
    }
}
