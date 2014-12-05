<?php
//ダウンロードフォルダ
$filename = WCE_PLUGIN_DIR . '/download/';

//投稿タイプを取得
$post_types = get_post_types( array(), "objects" );

//特定の投稿タイプを削除
unset(
    $post_types['attachment'],
    $post_types['revision'],
    $post_types['nav_menu_item'],
    $post_types['acf'],
    $post_types['wpcf7_contact_form']
);

//タクソノミーを取得
$post_taxonomies = get_taxonomies( array(), "objects" );

//特定のタクソノミー削除
unset(
    $post_taxonomies['page'],
    $post_taxonomies['post_tag'],
    $post_taxonomies['nav_menu'],
    $post_taxonomies['link_category'],
    $post_taxonomies['post_format']
);

$is_full_type = ( get_option( 'wce_full_type' ) === 'wp-csv-exporter' ) ? true : false;
// $is_full_type = true;
?>
<script type="text/javascript">
jQuery(function($){

    //カレンダー / 公開日
    var post_dates = jQuery( '#post_date-datepicker-from, #post_date-datepicker-to' ) . datepicker( {
        showAnim: 'clip',
        changeMonth: true,
        numberOfMonths: 3,
        showCurrentAtPos: 1,
        onSelect: function( selectedDate ) {
            var option = this . id == 'post_date-datepicker-from' ? 'minDate' : 'maxDate',
                instance = jQuery( this ) . data( 'datepicker' ),
                date = jQuery . datepicker . parseDate(
                    instance . settings . dateFormat ||
                    jQuery . datepicker . _defaults . dateFormat,
                    selectedDate, instance . settings );
            dates . not( this ) . datepicker( 'option', option, date );
        }
    } );
    post_dates.datepicker("option", "dateFormat", 'yy-m-d');

    //カレンダー / 更新日
    var post_modifieds = jQuery( '#post_modified-datepicker-from, #post_modified-datepicker-to' ) . datepicker( {
        showAnim: 'clip',
        changeMonth: true,
        numberOfMonths: 3,
        showCurrentAtPos: 1,
        onSelect: function( selectedDate ) {
            var option = this . id == 'post_modified-datepicker-from' ? 'minDate' : 'maxDate',
                instance = jQuery( this ) . data( 'datepicker' ),
                date = jQuery . datepicker . parseDate(
                    instance . settings . dateFormat ||
                    jQuery . datepicker . _defaults . dateFormat,
                    selectedDate, instance . settings );
            dates . not( this ) . datepicker( 'option', option, date );
        }
    } );
    post_modifieds.datepicker("option", "dateFormat", 'yy-m-d');

    //チェックボックス
    $('.all_checked').click(function(){
        var target = $(this).attr('data-target');
        $(target).prop('checked', true);
    });
    $('.all_checkout').click(function(){
        var target = $(this).attr('data-target');
        $(target).prop('checked', false);
    });

<?php 
foreach ( $post_types as $post_type ):
    if( $post_type->name == 'post' || $is_full_type):
?>
$('#form_<?php echo esc_attr( $post_type->name ) ?>').submit(function(){
    var total_check_num = $("#form_<?php echo esc_attr( $post_type->name ) ?> input.post_status:checked").length;
    if(total_check_num == 0){
        alert('<?php $this->e( '"Status" is a required field.', '"ステータス"は必須項目です' ) ?>');
        return false;
    }

    //件数
    if(!$('#form_<?php echo esc_attr( $post_type->name ) ?> input.limit').val().match(/^[0-9]+$/)){
        alert('<?php $this->e( 'The number of articles must be entered in numerical format.', '記事数は数値のみが入力可能です。' ) ?>');
        return false;
    }
});
<?php 
    endif;
endforeach;
?>
});
</script>

<div class="wrap plugin-wrap">

<div class="plugin-main-area">
<h2><?php $this->e( 'WP CSV Exporter', 'WP CSV Exporter' ) ?></h2>
<p>
<?php $this->e( 'Please set the fields you would like to export with CSV.', 'CSVでエクスポートする項目を設定してください。' ) ?>
</p>

<?php if ( !is_writable( $filename ) ) : ?>
<div class="error">
    <p>
        <?php $this->e( 'Please adjust your permissions so that you are able to edit the below directory.', '以下のディクレトリに書き込みができるようにパーミッションを変更してください。' ) ?><br>
        <strong><?php echo $filename; ?></strong>
    </p>
</div>
<?php endif; ?>

<ul class="plugin_tab">
<?php foreach ( $post_types as $post_type ): ?>
<li class="plugin_tab-<?php echo $post_type->name;?>"><?php echo $post_type->labels->name;?></li>
<?php 
endforeach;
?>
<li class="plugin_setting"><a href="<?php echo $this->plugin_setting_url ?>">Setting &gt;</a></li>
</ul>

<div class="plugin_contents">
<?php 
foreach ( $post_types as $post_type ): 
    if( $post_type->name == 'post' || $is_full_type):
?>
    <div class="plugin_content">
<form action="<?php echo WCE_PLUGIN_URL .'/admin/download.php'; ?>" method="post" id="form_<?php echo esc_attr( $post_type->name ) ?>" target="_blank">
<?php wp_nonce_field( 'csv_exporter' );?>

<div class="tool-box">
<h3><?php $this->e( 'Settings', '設定' ) ?></h3>
<ul class="setting_list">
    <li><label><input type="radio" name="post_id" value="post_id" checked="checked" required>*<?php $this->e( 'Post ID', '投稿ID' ) ?></label></li>
    <li><label><input type="radio" name="post_type" value="<?php echo esc_attr( $post_type->name ) ?>" checked="checked" required>*<?php $this->e( 'Post Type', '投稿タイプ' ) ?></label></li>
    <li><label><input type="checkbox" name="posts_values[]" value="post_name" checked="checked"><?php $this->e( 'Slug', 'スラッグ' ) ?></label></li>
    <li><label><input type="checkbox" name="posts_values[]" value="post_title" checked="checked"><?php $this->e( 'Post Title', '記事タイトル' ) ?></label></li>
    <li><label><input type="checkbox" name="posts_values[]" value="post_content" checked="checked"><?php $this->e( 'Post Content', '記事本文' ) ?></label></li>
    <li><label><input type="checkbox" name="posts_values[]" value="post_excerpt"><?php $this->e( 'Post Excerpt', '抜粋' ) ?></label></li>
    <li><label><input type="checkbox" name="post_thumbnail" value="post_thumbnail"><?php $this->e( 'Thumbnail', 'アイキャッチ画像' ) ?></label></li>
    <li><?php $this->e( 'Status', 'ステータス' ) ?>
    <ul>
        <li><label><input type="checkbox" name="post_status[]" value="publish" class="post_status" checked="checked"><?php $this->e( 'Publish', '公開済み（publish）' ) ?></label></li>
        <li><label><input type="checkbox" name="post_status[]" value="pending" class="post_status" ><?php $this->e( 'Pending', 'レビュー待ち（pending）' ) ?></label></li>
        <li><label><input type="checkbox" name="post_status[]" value="draft" class="post_status" ><?php $this->e( 'Draft', '下書き（draft）' ) ?></label></li>
        <li><label><input type="checkbox" name="post_status[]" value="future" class="post_status" ><?php $this->e( 'Future', 'スケジュール済み（future）' ) ?></label></li>
        <li><label><input type="checkbox" name="post_status[]" value="private" class="post_status" ><?php $this->e( 'Private', '非公開（private）' ) ?></label></li>
        <li><label><input type="checkbox" name="post_status[]" value="trash" class="post_status" ><?php $this->e( 'Trash', 'ゴミ箱入り（trash）' ) ?></label></li>
        <li><label><input type="checkbox" name="post_status[]" value="inherit" class="post_status" ><?php $this->e( 'Inherit', 'inherit' ) ?></label></li>
    </ul>
    </li>
    <li><label><input type="checkbox" name="posts_values[]" value="post_author"><?php $this->e( 'Author', '投稿者' ) ?></label></li>
    <li><label><input type="checkbox" name="posts_values[]" value="post_date"><?php $this->e( 'Post Date', '公開日時' ) ?></label></li>
    <li><label><input type="checkbox" name="posts_values[]" value="post_modified"><?php $this->e( 'Date Modified', '変更日時' ) ?></label></li>
    <li><label><input type="checkbox" name="post_tags" value="post_tags"><?php $this->e( 'Tags', 'タグ' ) ?></label></li>
</ul>
</div>

<hr>

<div class="tool-box">
<h3><?php $this->e( 'Taxonomies', 'タクソノミー' ) ?>
<span class="all_checks">
[
<a href="javascript:void(0);" class="all_checked" data-target="#form_<?php echo esc_attr( $post_type->name ) ?> .cf_taxonomy"><?php $this->e( 'Select all', '全選択' ) ?></a>
<a href="javascript:void(0);" class="all_checkout" data-target="#form_<?php echo esc_attr( $post_type->name ) ?> .cf_taxonomy"><?php $this->e( 'Unselect all', '全解除' ) ?></a>
]
</span>
</h3>
<ul class="setting_list">
<?php
$num = 0;
foreach ( $post_taxonomies as $post_taxonomy ):

    //オブジェクトタイプがタクソノミーを使用できるか調べる
    if ( !is_object_in_taxonomy( $post_type->name, $post_taxonomy->name ) ) {
        continue;
    }
?>
    <li><label><input type="checkbox" name="taxonomies[]" class="cf_taxonomy" checked="checked" value="<?php echo $post_taxonomy->name;?>" <?php selected( $_GET['engine'], '1000' );?>> <?php echo $post_taxonomy->labels->name;?></label></li>
<?php
$num ++;
endforeach;

if ( $num == 0 ):
?>
<li><?php $this->e( 'There are no registered custom taxonomies.', '登録されているカスタムタクソノミーはありません。' ) ?></li>
<?php endif; ?>
</ul>
</div>

<hr>

<div class="tool-box">
<h3><?php $this->e( 'Custom Fields', 'カスタムフィールド' ) ?>
<span class="all_checks">
[
<a href="javascript:void(0);" class="all_checked" data-target="#form_<?php echo esc_attr( $post_type->name ) ?> .cf_checkbox"><?php $this->e( 'Select all', '全選択' ) ?></a>
<a href="javascript:void(0);" class="all_checkout" data-target="#form_<?php echo esc_attr( $post_type->name ) ?> .cf_checkbox"><?php $this->e( 'Unselect all', '全解除' ) ?></a>
]
</span>
</h3>

<?php
//カスタムフィールドリストを取得
$cf_results = $this->get_custom_field_list( $post_type->name );
?>
<ul class="setting_list">
<?php if ( !empty( $cf_results ) ): ?>
<?php foreach ( $cf_results as $key => $value ) :?>
<li><label><input type="checkbox" name="cf_fields[]" class="cf_checkbox" checked="checked" value="<?php echo $value['meta_key']; ?>"> <?php echo $value['meta_key']; ?></label></li>
<?php endforeach;?>
<?php else: ?>
<li><?php $this->e( 'There are not registered custom fields.', '登録されているカスタムフィールドはありません。' ) ?></li>
</ul>
<?php endif; ?>
</div>

<hr>

<div class="tool-box">
<h3><?php $this->e( 'Others', 'その他' ) ?></h3>
<table class="setting_table">
<tbody>
<tr>
<th><?php $this->e( 'Number of articles to download.', 'ダウンロードする記事件数' ) ?></th>
<td><input type="text" name="limit" class="limit" value="0"> <?php $this->e( '*All downloaded if "0" selected.', '※0の場合はすべてダウンロード' ) ?></td>
</tr>
<tr>
    <th><?php $this->e( 'Select period to display.', '公開日の期間指定' ) ?></th>
    <td id="post_date-datepicker-wrap">
    <label for="post_date-datepicker-from">From</label>
    <input type="text" id="post_date-datepicker-from" name="post_date_from"/>
    <label for="post_date-datepicker-to">To</label>
    <input type="text" id="post_date-datepicker-to" name="post_date_to"/>
    </td>
</tr>
<tr>
    <th><?php $this->e( 'Select date modified.', '変更日の期間指定' ) ?></th>
    <td id="post_modified-datepicker-wrap">
    <label for="post_modified-datepicker-from">From</label>
    <input type="text" id="post_modified-datepicker-from" name="post_modified_from"/>
    <label for="post_modified-datepicker-to">To</label>
    <input type="text" id="post_modified-datepicker-to" name="post_modified_to"/>
    </td>
</tr>
<tr class="vt">
    <th><span><?php $this->e( 'Character Code', '文字コード' ) ?></span></th>
    <td>
<ul class="setting_list">
<li><label><input type="radio" name="string_code" value="UTF-8" checked="checked"> UTF-8</label></li>
<li><label><input type="radio" name="string_code" value="SJIS"> Shift_JIS</label></li>
</ul>
    </td>
</tr>
</tbody>
</table>

</div>

<p class="submit"><input type="submit" class="button-primary" value="<?php $this->e( 'Export', 'エクスポート' ) ?> <?php echo $post_type->labels->name;?> CSV" <?php if ( !is_writable( $filename ) ) : ?>disabled<?php endif; ?> /></p>
</form>
</div>
<?php else: ?>
    <div class="plugin_content">
        <p>この投稿タイプのCSVをダウンロードするにはライセンスキーを登録してください。</p> 
        <p><a href="<?php echo $this->plugin_setting_url ?>">ライセンスキーの登録</a></p>
    </div>
<?php 
    endif;
endforeach;
?>
</div>
</div><!-- /.plugin-main-area -->

<!-- .plugin-side-area -->
<div class="plugin-side-area">
<div class="plugin-side">
<div class="inner">

<div class="box">
<?php $this->e( 'The detailed explanation of this plugin is this url.' );?>
<a href="http://www.kigurumi.asia/imake/2548/" target="_blank">http://www.kigurumi.asia/imake/2548/</a>
<iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fkigurumi.oihagi&amp;width=278&amp;height=62&amp;show_faces=false&amp;colorscheme=light&amp;stream=false&amp;show_border=false&amp;header=false&amp;appId=355939381181327" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:62px;" allowTransparency="true"></iframe>
</div>

<div class="box">
<?php printf( $this->_( 'If you find this plugin usefull, don\'t hesitate to buy me some present from <a href="%s" target="_blank">my wishlist</a>.' ), 'http://www.amazon.co.jp/registry/wishlist/2TUGZOYJW8T4T/?tag=wpccc-22' ); ?>
</div>

<div class="box">
<a href="https://twitter.com/intent/tweet?screen_name=kanakogi" class="twitter-mention-button" data-lang="ja" data-related="kanakogi">Tweet to @kanakogi</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</div>

</div>
</div>
</div>
<!-- /.plugin-side-area -->

</div>
