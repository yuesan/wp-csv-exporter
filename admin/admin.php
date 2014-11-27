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

<?php foreach ( $post_types as $post_type ):?>
$('#form_<?php echo esc_attr( $post_type->name ) ?>').submit(function(){
	var total_check_num = $("#form_<?php echo esc_attr( $post_type->name ) ?> input.post_status:checked").length;
	if(total_check_num == 0){
      	alert("ステータスは必須項目です");
		return false;
	}

	//件数
	if(!$('#form_<?php echo esc_attr( $post_type->name ) ?> input.limit').val().match(/^[0-9]+$/)){
		alert("記事数は数値のみです");
		return false;
	}
});
<?php endforeach;?>
});
</script>

<div class="wrap">
<h2>WP CSV Exporter</h2>
<p>CSVでエクスポートする項目を設定してください。</p>

<?php if ( !is_writable( $filename ) ) : ?>
<div class="error">
    <p>
        以下のディクレトリに書き込みができるようにパーミッションを変更してください。<br>
        <strong><?php echo $filename; ?></strong>
    </p>
</div>
<?php endif; ?>

<ul class="plugin_tab">
<?php foreach ( $post_types as $post_type ):?>
<li class="plugin_tab-<?php echo $post_type->name;?>"><?php echo $post_type->labels->name;?></li>
<?php endforeach;?>
</ul>

<div class="plugin_contents">
<?php foreach ( $post_types as $post_type ):?><div class="plugin_content">
<form action="<?php echo WCE_PLUGIN_URL .'/admin/download.php'; ?>" method="post" id="form_<?php echo esc_attr( $post_type->name ) ?>" target="_blank">
<?php wp_nonce_field( 'csv_exporter' );?>

<div class="tool-box">
<h3>設定</h3>
<ul class="setting_list">
    <li><label><input type="radio" name="post_id" value="post_id" checked="checked" required>*投稿ID</label></li>
    <li><label><input type="radio" name="post_type" value="<?php echo esc_attr( $post_type->name ) ?>" checked="checked" required>*投稿タイプ</label></li>
    <li><label><input type="checkbox" name="posts_value[]" value="post_name" checked="checked">スラッグ</label></li>
    <li><label><input type="checkbox" name="posts_value[]" value="post_title" checked="checked">タイトル</label></li>
    <li><label><input type="checkbox" name="posts_value[]" value="post_content" checked="checked">本文</label></li>
    <li>ステータス
    <ul>
        <li><label><input type="checkbox" name="post_status[]" value="publish" class="post_status" checked="checked">公開済み</label></li>
        <li><label><input type="checkbox" name="post_status[]" value="pending" class="post_status" >レビュー待ち</label></li>
        <li><label><input type="checkbox" name="post_status[]" value="draft" class="post_status" >下書き</label></li>
        <li><label><input type="checkbox" name="post_status[]" value="future" class="post_status" >スケジュール済み</label></li>
        <li><label><input type="checkbox" name="post_status[]" value="private" class="post_status" >非公開</label></li>
        <li><label><input type="checkbox" name="post_status[]" value="trash" class="post_status" >ゴミ箱入り</label></li>
        <li><label><input type="checkbox" name="post_status[]" value="inherit" class="post_status" >inherit</label></li>
    </ul>
    </li>
    <li><label><input type="checkbox" name="posts_value[]" value="post_author">投稿者</label></li>
    <li><label><input type="checkbox" name="posts_value[]" value="post_date">公開日時</label></li>
    <li><label><input type="checkbox" name="posts_value[]" value="post_modified">更新日時</label></li>
    <li><label><input type="checkbox" name="post_tags" value="post_tags">タグ</label></li>
</ul>
</div>

<hr>

<div class="tool-box">
<h3><?php echo $post_type->labels->name ?>のタクソノミー
<span class="all_checks">
[
<a href="javascript:void(0);" class="all_checked" data-target="#form_<?php echo esc_attr( $post_type->name ) ?> .cf_taxonomy">全選択</a>
<a href="javascript:void(0);" class="all_checkout" data-target="#form_<?php echo esc_attr( $post_type->name ) ?> .cf_taxonomy">全解除</a>
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
<li>登録されているカスタムタクソノミーはありません。</li>
<?php endif; ?>
</ul>
</div>

<hr>

<div class="tool-box">
<h3><?php echo $post_type->labels->name ?>のカスタムフィールド
<span class="all_checks">
[
<a href="javascript:void(0);" class="all_checked" data-target="#form_<?php echo esc_attr( $post_type->name ) ?> .cf_checkbox">全選択</a>
<a href="javascript:void(0);" class="all_checkout" data-target="#form_<?php echo esc_attr( $post_type->name ) ?> .cf_checkbox">全解除</a>
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
<li>登録されているカスタムフィールドはありません。</li>
</ul>
<?php endif; ?>
</div>

<hr>

<div class="tool-box">
<h3>その他</h3>
<table class="setting_table">
<tbody>
<tr>
<th>記事数</th>
<td><input type="text" name="limit" class="limit" value="0"> ※0の場合はすべてダウンロード</td>
</tr>
<tr>
    <th>"公開日"の期間指定</th>
    <td id="post_date-datepicker-wrap">
    <label for="post_date-datepicker-from">From</label>
    <input type="text" id="post_date-datepicker-from" name="post_date_from"/>
    <label for="post_date-datepicker-to">To</label>
    <input type="text" id="post_date-datepicker-to" name="post_date_to"/>
    </td>
</tr>
<tr>
    <th>"更新日"の期間指定</th>
    <td id="post_modified-datepicker-wrap">
    <label for="post_modified-datepicker-from">From</label>
    <input type="text" id="post_modified-datepicker-from" name="post_modified_from"/>
    <label for="post_modified-datepicker-to">To</label>
    <input type="text" id="post_modified-datepicker-to" name="post_modified_to"/>
    </td>
</tr>
<tr class="vt">
    <th><span>文字コード</span></th>
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

<p class="submit"><input type="submit" id="post_csv" class="button-primary" value="エクスポート" <?php if ( !is_writable( $filename ) ) : ?>disabled<?php endif; ?> /></p>
</form>
</div>
<?php endforeach;?>
</div>
</div>