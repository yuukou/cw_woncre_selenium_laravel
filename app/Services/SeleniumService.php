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
        // 実行時間を無制限に変更
        set_time_limit(0);

        // selenium
        $host = 'http://localhost:4444/wd/hub';
        // chrome ドライバーの起動
        $driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
        // 画面サイズをMAXに
        $driver->manage()->window()->maximize();
        // 指定URLへ遷移 (Google)
        $driver->get('https://www.furimawatch.net/tool/#!/login');

        // Google認証処理
        if ($authentication === 'google') {
            $this->googleAuthentication($driver, $email, $passWord);
        }

        // Facebook認証処理
        if ($authentication === 'facebook') {
            $this->facebookAuthentication($driver, $email, $passWord);
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
            if (strlen($csv[0])) {
                $driver->findElement(WebDriverBy::id("inputKwAll"))->sendKeys($csv[0]);
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
            if ($this->isDecimal($csv[21])) {
                if ($this->isInt($csv[21])) {
                    $driver->findElement(WebDriverBy::id("inputPmin"))->sendKeys($csv[21]);
                }
            }

            # 上限値段
            if ($this->isDecimal($csv[22])) {
                if ($this->isInt($csv[22])) {
                    $driver->findElement(WebDriverBy::id("inputPmax"))->sendKeys($csv[22]);
                }
            }

            # アラート名
            if (strlen($csv[23])) {
                $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[15]/div/label/input"))->click();
                $driver->findElement(WebDriverBy::id("inputName"))->sendKeys($csv[23]);
            }

            # アラートのプレビュー押下
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/button"))->click();

            # 画面遷移のため1秒間停止
            sleep(1);

            # これでOKを押下
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/div[2]/button"))->click();

            # 画面遷移のため5秒間停止（登録処理が走るので少し長めに設定）
            $driver->wait()->until(
                WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::xpath("/html/body/div[1]/div/button"))
            );

            // 最後はwindowを閉じる
            if ($i === $length-1) {
                $driver->close();
            }
        }
    }

    /**
     * Excel等で省略で数値の省略値で入ってきた際に入力させない
     *
     * @param string $value
     * @return bool
     */
    private function isDecimal(string $value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * 整数値かどうかの判定
     *
     * @param string $num
     * @return bool
     */
    private function isInt(string $num)
    {
        if(preg_match("/^[0-9]+$/",$num)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Google認証処理
     *
     * @param RemoteWebDriver $driver
     * @param $email
     * @param $passWord
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    private function googleAuthentication(RemoteWebDriver $driver, $email, $passWord)
    {
        // google認証ボタンクリック
        $driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div/button[1]'))->click();

        # ウィンドウ移動のため1秒間停止
        sleep(1);

        # ウィンドウハンドルを取得する
        $handleArray = $driver->getWindowHandles();

        # seleniumで操作可能なdriverを切り替える
        $driver->switchTo()->window($handleArray[1]);

        # type email
        $driver->findElement(WebDriverBy::name("identifier"))->sendKeys($email);

        # click next
        $driver->findElement(WebDriverBy::id("identifierNext"))->click();

        # 画面遷移のため3秒間停止
        sleep(3);

        # type password
        $driver->findElement(WebDriverBy::name("password"))->sendKeys($passWord);

        # click signin
        $driver->findElement(WebDriverBy::id("passwordNext"))->click();

        # 画面遷移のため5秒間停止
        sleep(5);

        // googleの認証処理が全て終わるまで待つ
        $driver->wait()->until(
            WebDriverExpectedCondition::numberOfWindowsToBe(1)
        );

        # seleniumで操作可能なdriverを切り替える
        $driver->switchTo()->window($handleArray[0]);

        // 認証処理が通り、ログイン状態になるまで待つ
        $driver->wait()->until(
            WebDriverExpectedCondition::elementTextContains(WebDriverBy::xpath("/html/body/nav/ul/li[3]/a"), 'ログアウト')
        );
    }

    /**
     * Facebook認証処理
     *
     * @param RemoteWebDriver $driver
     * @param $email
     * @param $passWord
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    private function facebookAuthentication(RemoteWebDriver $driver, $email, $passWord)
    {
        // Facebook認証ボタンクリック
        $driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div/button[2]'))->click();

        # ウィンドウ移動のため1秒間停止
        sleep(1);

        # ウィンドウハンドルを取得する
        $handleArray = $driver->getWindowHandles();

        # seleniumで操作可能なdriverを切り替える
        $driver->switchTo()->window($handleArray[1]);

        # type email
        $driver->findElement(WebDriverBy::name("email"))->sendKeys($email);

        # type password
        $driver->findElement(WebDriverBy::name("pass"))->sendKeys($passWord);

        # click next
        $driver->findElement(WebDriverBy::xpath('//*[@id="loginbutton"]'))->click();

        # seleniumで操作可能なdriverを切り替える
        $driver->switchTo()->window($handleArray[0]);

        // ログアウトの文字が表示されるまで待つ
        $driver->wait()->until(
            WebDriverExpectedCondition::elementTextContains(WebDriverBy::xpath("/html/body/nav/ul/li[3]/a"), 'ログアウト')
        );
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
            if (strlen($csv[$i])) {
                # 除外キーワード１
                $wordArray[] = $csv[$i];
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
        if (!($csv[11] === 'o' || $csv[11] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[1]/label"))->click();
        }

        // フリル (o/O以外)
        if (!($csv[12] === 'o' || $csv[12] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[2]/label"))->click();
        }

        // ラクマ (o/O以外)
        if (!($csv[13] === 'o' || $csv[13] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[3]/label"))->click();
        }

        // オタマート (o/O以外)
        if (!($csv[14] === 'o' || $csv[14] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[4]/label"))->click();
        }

        // ZOZO (o/O以外)
        if (!($csv[15] === 'o' || $csv[15] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[5]/label"))->click();
        }

        // チケットキャンプ (o/O以外)
        if (!($csv[16] === 'o' || $csv[16] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[6]/label"))->click();
        }

        // ショッピーズ (o/O以外)
        if (!($csv[17] === 'o' || $csv[17] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[7]/label"))->click();
        }

        // ヤフオク (o/O以外)
        if (!($csv[18] === 'o' || $csv[18] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[8]/label"))->click();
        }

        // ブクマ (o/O以外)
        if (!($csv[19] === 'o' || $csv[19] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[9]/label"))->click();
        }

        // モノキュン (o/O以外)
        if (!($csv[20] === 'o' || $csv[20] === 'O')) {
            $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/div[12]/div[10]/label"))->click();
        }
    }
}
