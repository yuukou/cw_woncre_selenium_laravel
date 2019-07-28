<?php

namespace App\Http\Services;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class SeleniumService
{
    /**
     * seleniumの自動登録を実行
     *
     * @param $email
     * @param $passWord
     * @param $authentication
     * @param array $csvArray
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function exec($email, $passWord, $authentication, array $csvArray)
    {
        // selenium
        $host = 'http://localhost:4444/wd/hub';
        // chrome ドライバーの起動
        $driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
        // 画面サイズをMAXに
        $driver->manage()->window()->maximize();
        // 指定URLへ遷移 (Google)
        $driver->get('https://www.furimawatch.net/tool/#!/login');

        if ($authentication === 'google') {
            // google認証ボタンクリック
            $driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div/button[1]'))->click();

            # ウィンドウ移動のため1秒間停止
            sleep(1);

            # ウィンドウハンドルを取得する
            $handleArray = $driver->getWindowHandles();

            # seleniumで操作可能なdriverを切り替える
            $driver->switchTo()->window($handleArray[1]);

            # type email
            // Todo: tanaka 認証情報もcsvアップロードと同時に入力してもらうようにする
            $driver->findElement(WebDriverBy::name("identifier"))->sendKeys($email);

            # click next
            $driver->findElement(WebDriverBy::id("identifierNext"))->click();

            # 画面遷移のため3秒間停止
            sleep(3);

            # type password
            // Todo: tanaka 認証情報もcsvアップロードと同時に入力してもらうようにする
            $driver->findElement(WebDriverBy::name("password"))->sendKeys($passWord);

            # click signin
            $driver->findElement(WebDriverBy::id("passwordNext"))->click();

            sleep(5);

            # seleniumで操作可能なdriverを切り替える
            $driver->switchTo()->window($handleArray[0]);
        }

        # アラートボタン押下
        $driver->findElement(WebDriverBy::xpath("/html/body/nav/ul/li[1]/a"))->click();

        # 画面遷移のため1秒間停止
        sleep(1);

        # 新しいアラートの作成を押下
        $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/button"))->click();

        # 画面遷移のため1秒間停止
        sleep(1);

        // 配列の長さ
        $length = count($csvArray);
        foreach ($csvArray as $i => $csv) {
            if ($i !== 0) {
                # 新しいアラートの作成を押下
                $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/button"))->click();
            }

            # キーワード
            if (strlen($csv['keyword'])) {
                $driver->findElement(WebDriverBy::id("inputKwAll"))->sendKeys($csv['keyword']);
            }

            // 除外ワード登録用のワードの配列を作成
            $wordArray = $this->createFormatWordArray($csv);

            // 除外ワード登録
            foreach ($wordArray as $key => $word) {
                $driver->findElement(WebDriverBy::id("inputKwe".($key+1)))->sendKeys($word);
            }

            # 対象サービス
            $this->targetServiceList($driver, $csv);

            # 下限値段
            if (is_numeric($csv['lower'])) {
                if (is_int($csv['lower'])) {
                    $driver->findElement(WebDriverBy::id("inputPmin"))->sendKeys($csv['lower']);
                }
            }

            # 上限値段
            if (is_numeric($csv['max'])) {
                if (is_int($csv['max'])) {
                    $driver->findElement(WebDriverBy::id("inputPmax"))->sendKeys($csv['max']);
                }

            }

            # アラート名
            if (strlen($csv['alert'])) {
                $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[15]/div/label/input"))->click();
                $driver->findElement(WebDriverBy::id("inputName"))->sendKeys($csv['alert']);
            }

            # アラートのプレビュー押下
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/button"))->click();

            # 画面遷移のため1秒間停止
            sleep(1);

            # これでOKを押下
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/div[2]/button"))->click();

            if ($i === $length-1) {
                break;
            } else {
                # 画面遷移のため5秒間停止（登録処理が走るので少し長めに設定）
                $driver->wait()->until(
                    WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::xpath("/html/body/div[1]/div/button"))
                );
            }
        }
        $driver->close();
    }

    /**
     * 除外ワード用の配列を作成
     *
     * @param array $csv
     * @return array
     */
    private function createFormatWordArray(array $csv)
    {
        $wordArray = [];
        for ($i=1; $i<=10; $i++) {
            if (strlen($csv['word'.$i])) {
                # 除外キーワード１
                $wordArray[] = $csv['word'.$i];
            }
        }

        return $wordArray;
    }

    /**
     * サービスのチェック状態はデフォルトでチェック状態なので、csvが[o]/[O]以外のときにチェックを外す
     *
     * @param RemoteWebDriver $driver
     * @param array $csv
     */
    private function targetServiceList(RemoteWebDriver $driver, array $csv)
    {
        // メルカリ (o/O以外)
        if (!($csv['merukari'] === 'o' || $csv['merukari'] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[1]/label"))->click();
        }

        // フリル (o/O以外)
        if (!($csv['furiru'] === 'o' || $csv['furiru'] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[2]/label"))->click();
        }

        // ラクマ (o/O以外)
        if (!($csv['rakuma'] === 'o' || $csv['rakuma'] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[3]/label"))->click();
        }

        // オタマート (o/O以外)
        if (!($csv['otama'] === 'o' || $csv['otama'] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[4]/label"))->click();
        }

        // ZOZO (o/O以外)
        if (!($csv['zozo'] === 'o' || $csv['zozo'] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[5]/label"))->click();
        }

        // チケットキャンプ (o/O以外)
        if (!($csv['ticket'] === 'o' || $csv['ticket'] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[6]/label"))->click();
        }

        // ショッピーズ (o/O以外)
        if (!($csv['shopiz'] === 'o' || $csv['shopiz'] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[7]/label"))->click();
        }

        // ヤフオク (o/O以外)
        if (!($csv['yahoo'] === 'o' || $csv['yahoo'] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[8]/label"))->click();
        }

        // ブクマ (o/O以外)
        if (!($csv['bukuma'] === 'o' || $csv['bukuma'] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[9]/label"))->click();
        }

        // モノキュン (o/O以外)
        if (!($csv['monokyun'] === 'o' || $csv['monokyun'] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[10]/label"))->click();
        }
    }
}
