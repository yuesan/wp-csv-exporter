<?php 
require_once WCE_PLUGIN_DIR . '/admin/functions.php';

if(
    isset( $_POST['license_key'] ) &&
    is_user_logged_in() &&
    isset( $_POST['_wpnonce'] ) &&
    wp_verify_nonce( $_POST['_wpnonce'], 'csv_exporter' ) 
   ){
    //エラー
    $e = new WP_Error();
    $license_key = esc_html($_POST['license_key']);

    //認証
    if( $this->verify_license_key($license_key) ){
        $e->add( 'error', $this->_( "License key has been authenticated.", 'ライセンスキーが認証されました。' ) );
        set_transient( 'post-updated', $e->get_error_messages(), 1 );
    }else{
        $e->add( 'error', $this->_( "License key authentication failed.", 'ライセンスキーの認証に失敗しました。' ) );
        set_transient( 'post-error', $e->get_error_messages(), 1 );
    }

}

//保存成功
if ( $messages = get_transient( 'post-updated' ) ) {
    display_messages( $messages, 'updated' );

//保存失敗
}elseif ( $messages = get_transient( 'post-error' ) ) {
    display_messages( $messages, 'error' );
}
?>
<div class="wrap plugin-wrap">

    <div class="plugin-main-area">
        <h2><?php $this->e('WP CSV Exporter', 'WP CSV Exporter') ?></h2>
        <p>
            <?php $this->e( 'License key input.', 'ライセンスキーの入力。' ) ?>
        </p>

        <ul class="plugin_tab">
            <li class="plugin_tab"><a href="<?php echo $this->plugin_admin_url ?>">CSV <?php $this->e('Export', 'エクスポート') ?></a></li>    
            <li class="select">Setting</li>
        </ul>

        <div class="plugin_contents">

            <div class="plugin_content">
                <form action="" method="post" id="form">
                    <?php wp_nonce_field( 'csv_exporter' );?>
                    <div class="tool-box">
                        <p>
                            <?php $this->e( 'Inputting a license key will enable you to download CSVs for static pages and custom post types in addition to those for posts.', 'ライセンスキーを入力すると「投稿」以外の「固定ページ」や「カスタム投稿タイプ」のCSVもダウンロードが出来るようになります。' ) ?>
                        </p>
                        <p>
                            <?php $this->e( 'A license key can be purchased from the website below.', 'ライセンスキーは、以下のWEBサイトから購入ができます。' ) ?>
                        </p>
                          <table class="setting_table">
                            <tbody>
                                <tr>
                                    <th>- <a href="https://gumroad.com/l/DRHMU" target="_blank">Gumroad</a></th>
                                    <td>$9.8</td>
                                </tr>
                                <tr>
                                    <th>- <a href="https://flipclap.stores.jp/#!/items/5480e8583cd482f22b001f7e" target="_blank">STORES.JP</a></th>
                                    <td>¥980</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <table class="setting_table">
                            <tbody>
                                <tr>
                                    <th><?php $this->e('license key') ?></th>
                                    <td>
                                        <input type="text" id="wce-license-key" name="license_key" class="license_key" value="<?php echo esc_html($_POST['license_key']) ?>" style="width:350px">
                                        <?php 
                                        if( $this->is_certified() ){
                                            echo '<strong>'.$this->_('Authenticated', '認証済み').'</strong>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="submit"><input type="submit" id="wce-lsubmit" class="button-primary" value="<?php $this->e('Submit') ?>" /></p>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.plugin-main-area -->

</div>