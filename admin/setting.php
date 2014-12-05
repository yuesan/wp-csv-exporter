<div class="wrap plugin-wrap">

    <div class="plugin-main-area">
        <h2><?php $this->e('WP CSV Exporter', 'WP CSV Exporter') ?></h2>
        <p>
            ライセンスキーの入力。
        </p>

        <ul class="plugin_tab">
            <li class="plugin_tab"><a href="<?php echo $this->plugin_admin_url ?>">CSV <?php $this->e('Export', 'エクスポート') ?></a></li>    
            <li class="select">Setting</li>
        </ul>

        <div class="plugin_contents">

            <div class="plugin_content">
                <form action="http://services.flipclap.co.jp/wp-csv-exporter/license-key.php" method="post" id="form">
                    <div class="tool-box">
                        <p>
                            ライセンスキーを入力すると「投稿」以外の「固定ページ」や「カスタム投稿タイプ」のCSVもダウンロードが出来るようになります。
                        </p>
                        <p>
                            ライセンスキーは、以下のWEBサイトから購入ができます。
                        </p>
                          <table class="setting_table">
                            <tbody>
                                <tr>
                                    <th>- <a href="https://gumroad.com/l/DRHMU" target="_blank">Gumroad</a></th>
                                    <td>$9.8</td>
                                </tr>
                                <tr>
                                    <th>- <a href="https://kanakogi.stores.jp/#!/items/5480e8583cd482f22b001f7e" target="_blank">STORES.JP</a></th>
                                    <td>¥980</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <table class="setting_table">
                            <tbody>
                                <tr>
                                    <th><?php $this->e('license key') ?></th>
                                    <td><input type="text" id="wce-license-key" name="license-key" class="license-key" value="" style="width:350px"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="back_url" value="<?php echo (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" >
                    <p class="submit"><input type="submit" id="wce-lsubmit" class="button-primary" value="<?php $this->e('Submit') ?>" /></p>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.plugin-main-area -->

</div>