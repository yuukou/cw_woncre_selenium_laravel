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

        $csv = $this->seleniumService->parseCsv($request->csv_file);
        $this->seleniumService->exec($email, $passWord, $authentication, $csv);
        return Redirect::route('csv::index');
    }

    /**
     * アカウント認証用のデータを受信して、当該アカウントで登録済みのデータを一括削除する（削除対象のデータがなくなった場合、登録・削除画面にリダイレクト）
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function delete(Request $request)
    {
        if ($request->input('email') && $request->input('password') &&  $request->input('authentication')) {
            $email = $request->input('email');
            $passWord = $request->input('password');
            $authentication = $request->input('authentication');
        }

        $this->seleniumService->execDelete($email, $passWord, $authentication);
        return Redirect::route('csv::index');
    }
}
