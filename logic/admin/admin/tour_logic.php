<?php
require_once __DIR__ .  '/../../model/t_tour_model.php';
require_once __DIR__ .  '/../../logic/common/common_logic.php';


class tour_logic {
	private $t_tour_model;
	private $common_logic;

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		$this->t_tour_model= new t_tour_model();
		$this->common_logic = new common_logic ();
	}

	/**
	 * 初期HTML生成
	 */
	public function create_data_list($params, $search_select = null, $where = array()){


		$sqlAdd = $this->common_logic->create_where($search_select);


		if($where['tid'] != null && $where['tid'] != ''){
			$add = ($sqlAdd['where'] != null && $sqlAdd['where'] != '')?  " AND " : " WHERE ";
			$sqlAdd['where'] .= $add . " tour_id =  ? ";
			array_push($sqlAdd['whereParam'],  $where['tid']) ;
		}

		$page_title = 'サンプル';

		//総件数取得
		$result_cnt = $this->t_tour_model->get_tour_list_cnt($sqlAdd);

		$all_cnt = $result_cnt[0]['cnt'];
		$pager_cnt = ceil($all_cnt / $params[2]);
		$offset = ($params[1] - 1) * $params[2];

		$result_tour = $this->t_tour_model->get_tour_list($offset, $params[2],$sqlAdd);

		$return_html = "";
		$back_color = 1;
		$cnt = $offset;
		for($i = 0; $i < count ( $result_tour ); $i ++) {
			$row = $result_tour [$i];

			$cnt ++;
			$edit_html = '&nbsp;';

			$tour_id = $this->common_logic->zero_padding ( $row ['tour_id'] );

			//各データをhtmlに変換







			//画像表示処理
			$img_tag_html = '<img src="../assets/admin/img/nophoto.png" style="height:50px">';
			$nmage_list = array ();
			if (strpos ( $row ['image'], ',' ) !== false && ($row ['image'] != null && $row ['image'] != '')) {
				// 'abcd'のなかに'bc'が含まれている場合
				$img_tag_html = '';
				$nmage_list = explode ( ',', $row ['image'] );

				for($n = 0; $n < count ( $nmage_list ); $n ++) {
					$img_tag_html .= '<img src="../upload_files/tour/' . $nmage_list [$n] . '" style="height:50px">';
				}
			} else if ($row ['image'] != null && $row ['image'] != '') {
				$img_tag_html = '<img src="../upload_files/tour/' . $row ['image'] . '" style="height:50px">';
			}

			//動画
			if ($row['movie'] != null && $row['movie'] != ""){
				$movie = '<a  href="#modal" class="check_movie" tour_id="'. $row['tour_id'] .'">有り</a>';
			}else{
				$movie = '無し';
			}


			//削除フラグ
			$del_color = "";
			$edit_html_a = "<a herf='javascript:void(0);' class='edit clr1' name='edit_" . $row ['tour_id'] . "' value='" . $row ['tour_id'] . "'><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a><br>";
			$del_html = "有効";
			if ($row ['del_flg'] == 1) {
				$del_color = "color:#d3d3d3";
				$del_html = "削除";
				$edit_html_a .= "<a herf='javascript:void(0);' class='recovery clr2' name='recovery_" . $row ['tour_id'] . "' value='" . $row ['tour_id'] . "' ><i class=\"fa fa-undo\" aria-hidden=\"true\"></i></a><br>";
			} else {
				$edit_html_a .= "<a herf='javascript:void(0);' class='del clr2' name='del_" . $row ['tour_id'] . "' value='" . $row ['tour_id'] . "'><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></a><br>";
			}

			if ($back_color == 2) {
				$back_color_html = "style='background: #f7f7f9; " . $del_color . "'";
				$back_color_bottom_html = "style='background: #f7f7f9; border-bottom:solid 2px #d0d0d0;'";
			} else {
				$back_color_html = "style='background: #ffffff; " . $del_color . "'";
				$back_color_bottom_html = "style='background: #ffffff; border-bottom:solid 2px #d0d0d0;'";
			}

			$edit_html_b = '';
			$public_html = "公開";
			if ($row ['public_flg'] == 1) {
				$public_html = "非公開";
				$edit_html_b .= "<a herf='javascript:void(0);' class='release btn btn-default waves-effect w-md btn-xs' name='release_" . $row ['tour_id'] . "' value='" . $row ['tour_id'] . "'>非公開</a>";
			} else {
				$edit_html_b .= "<a herf='javascript:void(0);' class='private btn btn-custom waves-effect w-md btn-xs ' name='private_" . $row ['tour_id'] . "' value='" . $row ['tour_id'] . "'>公開</a>";
			}

			$create_at = $row['create_at'];
			$diff = strtotime(date('YmdHis')) - strtotime($create_at);
			if($diff < 60){
				$time = $diff;
				$create_at = $time . '秒前';
			}elseif($diff < 60 * 60){
				$time = round($diff / 60);
				$create_at = $time . '分前';
			}elseif($diff < 60 * 60 * 24){
				$time = round($diff / 3600);
				$create_at = $time . '時間前';
			}

			$update_at = $row['update_at'];
			$diff = strtotime(date('YmdHis')) - strtotime($update_at);
			if($diff < 60){
				$time = $diff;
				$update_at = $time . '秒前';
			}elseif($diff < 60 * 60){
				$time = round($diff / 60);
				$update_at = $time . '分前';
			}elseif($diff < 60 * 60 * 24){
				$time = round($diff / 3600);
				$update_at = $time . '時間前';
			}

			$store_data = $this->common_logic->select_logic("select * from t_store_basic where store_basic_id = ?", array($row['store_basic_id']));
			$store = '掲載社名：'.$store_data[0]['company_name'];




			$return_html .= "
					<tr " . $back_color_html . ">
						<td class='count_no'>" . $cnt . "</td>
						<td>" . $row['tour_id'] . "</td>
						<td>" . $row['title'] . "<br>" . $store . "</td>
						<td></td>
						<td>" . $create_at . "</td>
						<td>" . $update_at . "</td>
						<td>
							$edit_html_a
						</td>
						<td>
							$edit_html_b
						</td>
					</tr>
";
			$back_color ++;

			if ($back_color >= 3) {
				$back_color = 1;
			}
		}
		// }

		//ページャー部分HTML生成
		$pager_html = '<li><a href="javascript:void(0)" class="page prev" pager_type="prev">prev</a></li>';
		for ($i = 0; $i < $pager_cnt; $i++) {
			$disp_cnt = $i+1;

			if ($i == 0) {
				$pager_html .= '<li><a href="javascript:void(0)" class="page num_link" num_link="true" disp_id="'.$disp_cnt.'">'.$disp_cnt.'</a></li>';
			} else {
				$pager_html .= '<li><a href="javascript:void(0)" class="page num_link" num_link="true" disp_id="'.$disp_cnt.'">'.$disp_cnt.'</a></li>';
			}
		}
		$pager_html .= '<li><a href="javascript:void(0)" class="page next" pager_type="next">next</a></li>';

		return array (
				"entry_menu_list_html" => $admin_menu_list_html,
				"list_html" => $return_html,
				"pager_html" => $pager_html,
				'page_cnt' => $pager_cnt,
				'all_cnt' => $all_cnt,
				'disp_all' => $disp_all,
		);
	}


	/**
	 * 新規登録処理
	 */
	public function entry_new_data($params) {

		$result = $this->t_tour_model->entry_tour( $params );
		return true;
	}

	/**
	 * 取得処理
	 */
	public function get_detail($tour_id ){
		$result = $this->t_tour_model->get_tour_detail ( $tour_id );

		return  $result [0];
	}

	/**
	 * 編集更新処理
	 * @param unknown $post
	 */
	public function update_detail($params){

		$result = $this->t_tour_model->update_tour($params);
		return true;
	}

	/**
	 * 有効化処理
	 *
	 * @param unknown $id
	 */
	public function recoveryl_func($id) {
		$this->t_tour_model->recoveryl_tour ( $id );
	}


	/**
	 * 削除処理
	 *
	 * @param unknown $id
	 */
	public function del_func($id) {
		$this->t_tour_model->del_tour ( $id );
	}

	/**
	 * 非公開化処理
	 *
	 * @param unknown $id
	 */
	public function private_func($id) {
		$this->t_tour_model->private_tour ( $id );
	}


	/**
	 * 公開処理
	 *
	 * @param unknown $id
	 */
	public function release_func($id) {
		$this->t_tour_model->release_tour ( $id );
	}

}