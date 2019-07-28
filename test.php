<?php

require_once './vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;

/**
 * selenium facebook-webdriver 実行のサンプル
 */
function sample()
{
    // selenium
    $host = 'http://localhost:4444/wd/hub';
    // chrome ドライバーの起動
    $driver = RemoteWebDriver::create($host,DesiredCapabilities::chrome());
    // 画面サイズをMAXに
    $driver->manage()->window()->maximize();
    // 指定URLへ遷移 (Google)
    $driver->get('https://www.furimawatch.net/tool/#!/login');
    // google認証ボタンクリック
    $driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div/button[1]'))->click();

    # ウィンドウ移動のため1秒間停止
    sleep(1);

    # ウィンドウハンドルを取得する
    $handleArray = $driver->getWindowHandles();

    # seleniumで操作可能なdriverを切り替える
    $driver->switchTo()->window($handleArray[1]);

    # type email
    $driver->findElement(WebDriverBy::name("identifier"))->sendKeys("yuukou.triplejump0219@gmail.com");

    # click next
    $driver->findElement(WebDriverBy::id("identifierNext"))->click();

    # 画面遷移のため3秒間停止
    sleep(3);

    # type password
    $driver->findElement(WebDriverBy::name("password"))->sendKeys("yuukou0219");

    # click signin
    $driver->findElement(WebDriverBy::id("passwordNext"))->click();

    sleep(5);

    # seleniumで操作可能なdriverを切り替える
    $driver->switchTo()->window($handleArray[0]);

    # アラートボタン押下
    $driver->findElement(WebDriverBy::xpath("/html/body/nav/ul/li[1]/a"))->click();

    # 画面遷移のため1秒間停止
    sleep(1);

    # 新しいアラートの作成を押下
    $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/button"))->click();

    # 画面遷移のため1秒間停止
    sleep(1);

    # キーワード
    $driver->findElement(WebDriverBy::id("inputKwAll"))->sendKeys("あいうえお");

    # 除外キーワード１
    $driver->findElement(WebDriverBy::id("inputKwe1"))->sendKeys("かきくけこ");

    # 対象サービス

    # 下限値段
    $driver->findElement(WebDriverBy::id("inputPmin"))->sendKeys("222");

    # 上限値段
    $driver->findElement(WebDriverBy::id("inputPmax"))->sendKeys("2222");

    # アラート名
    # browser.find_element_by_id("inputNameAuto").send_keys("トリプルエス")

    # アラートのプレビュー押下
    $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/form/button"))->click();

    # 画面遷移のため1秒間停止
    sleep(1);

    # これでOKを押下
    $driver->findElement(WebDriverBy::xpath("/html/body/div[1]/div/div[2]/button"))->click();

    # 画面遷移のため5秒間停止（登録処理が走るので少し長めに設定）
    sleep(5);
}

// 実行
sample();