<?php
require_once dirname( __FILE__ ) . '/../../../../wp-load.php';
if ( isset( $_POST['post_type'] ) ) {
	global $wpdb;
	check_admin_referer( 'csv_export' );

	$post_type = get_post_type_object( $_POST['post_type'] );
	$posts_value = $_POST['posts_value'];
	$post_status = $_POST['post_status'];
	$limit = esc_html( $_POST['limit'] );
	$post_date_from = $_POST['post_date_from'];
	$post_date_to = $_POST['post_date_to'];
	$post_modified_from = $_POST['post_modified_from'];
	$post_modified_to = $_POST['post_modified_to'];
	$string_code = esc_html( $_POST['string_code'] );

	// wp_postsテーブルから指定したpost_typeの公開記事を取得
	$query_select = 'ID as ' . $_POST['post_id'] .', post_type, post_status';
	if ( isset( $posts_value ) ) {
		foreach ( $posts_value as $key => $value ) {
			$query_select .= ', ' . $value;
		}
	}

	//ステータスのSQL
	$query_where = '';
	foreach ( $post_status as $key => $status ) {
		$query_where .= "'".$status."'";
		if ( $status != end( $post_status ) ) {
			$query_where .= ', ';
		}
	}

	// SQL文作成
	$query = <<< EOL
SELECT {$query_select}
FROM {$wpdb->posts}
WHERE post_status IN ({$query_where})
AND post_type LIKE '{$post_type->name}'
EOL;
	//期間指定-公開日
	if ( !empty( $post_date_from ) && !empty( $post_date_to ) ) {
		$query .= "AND post_date BETWEEN '".$post_date_from."' AND '".$post_date_to."' ";
	}
	//期間指定-更新日
	if ( !empty( $post_modified_from ) && !empty( $post_modified_to ) ) {
		$query .= "AND post_modified BETWEEN '".$post_modified_from."' AND '".$post_modified_to."' ";
	}
	//記事数が指定されている時
	if ( !empty( $limit ) ) {
		$query .= "LIMIT ".intval( $limit ) . " ";
	}
	// echo $query;

	//DBから取得
	$results = $wpdb->get_results( $query, ARRAY_A );

	// カテゴリとタグのslugを追加
	$results = array_map( function ( $result ) {
			//マージ用の配列
			$customs_array = array();

			//タグ
			if ( !empty( $_POST['post_tags'] ) ) {
				$tags = get_the_tags( $result['post_id'], esc_html( $_POST['post_tags'] ) );
				if ( is_array( $tags ) ) {
					$term_value = urldecode( implode( ',', array_map(
								function ( $tag ) {
									return $tag->slug;
								},
								$tags
							) ) );
					$customs_array += array( $_POST['post_tags'] => $term_value );
				}
			}

			//カスタムタクソノミー
			if ( !empty( $_POST['taxonomies'] ) ) {
				foreach ( $_POST['taxonomies'] as $key => $taxonomy ) {
					$terms = get_the_terms( $result['post_id'], esc_html( $taxonomy ) );
					if ( is_array( $terms ) ) {
						$term_value = urldecode( implode( ',', array_map(
									function ( $term ) {
										return $term->slug;
									},
									$terms
								) ) );
						$customs_array += array( $taxonomy => $term_value );
					}
				}
			}

			// カスタムフィールドを取得
			$fields = get_post_custom( $result['post_id'] );
			if ( !empty( $fields ) && !empty( $_POST['cf_fields'] ) ) {
				foreach ( $fields as $key => $field ) {
					//チェックしたフィールドだけを取得
					if ( array_search( $key, $_POST['cf_fields'] ) !== false ) {
						//アンダーバーから始まるのは削除
						if ( !preg_match( '/^_.*/', $key ) ) {
							$customs_array += array( $key => $field[0] );
						}
					}
				}
			}

			return array_merge( $result, $customs_array );
		}
		, $results );

	// print_r( $results );
	// 項目名を取得
	$head[] = array_keys( $results[0] );

	// 先頭に項目名を追加
	$list = array_merge( $head, $results );

	// ファイルの保存場所を設定
	$filename = 'export-'.$post_type->name.'-'.date( "Y-m-d_H-i-s" ).'.csv';
	$filepath = './'.$filename;
	$fp = fopen( $filepath, 'w' );

	// 配列をカンマ区切りにしてファイルに書き込み
	foreach ( $list as $fields ) {
		//文字コード変換
		mb_convert_variables( $string_code, 'UTF-8', $fields );
		fputcsv( $fp, $fields );
	}

	//ダウンロードの指示
	header( 'Content-Type:application/octet-stream' );
	header( 'Content-Disposition:filename='.$filename );  //ダウンロードするファイル名
	header( 'Content-Length:' . filesize( $filepath ) );   //ファイルサイズを指定
	readfile( $filepath );  //ダウンロード
	fclose( $fp );
	unlink( $filepath );

}
