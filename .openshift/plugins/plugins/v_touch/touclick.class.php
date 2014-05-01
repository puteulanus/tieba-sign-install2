<?php
class touclick {
	public function touclickCheck($publicKey, $privateKey, $check_key, $check_address) {
		$url = "";
		$path = "";
		if (empty ( $publicKey ) || empty ( $privateKey ) || empty ( $check_key ) || empty ( $check_address ))
			return false;
		if (! $this->filterUrl ( $url, $path, $check_address ))
			return false;
		$path = $path . "?b=" . $publicKey . "&z=" . $privateKey . "&i=" . $check_key . "&p=".$_SERVER['REMOTE_ADDR']."&un=0&ud=0";
		$getres = $this->requestGet ( $url . $path );
		return ! ! strpos ( $getres, "[yes]" );
	}
	private function filterUrl(&$url, &$path, $check_address) {
		$check_address_arr = explode ( ",", $check_address );
		if (! is_array ( $check_address_arr ) || count ( $check_address_arr ) != 2)
			return false;
		$url_arr = explode ( ".", $check_address_arr [0] );
		if (! is_array ( $url_arr ) || count ( $url_arr ) != 3)
			return false;
		$path_arr = explode ( ".", $check_address_arr [1] );
		if (! is_array ( $path_arr ) || count ( $path_arr ) != 2)
			return false;
		if (! $this->filterStr ( $url_arr [0] ) || ! $this->filterStr ( $path_arr [0] ))
			return false;
		$url = "http://" . $url_arr [0] . ".touclick.com";
		$path = "/" . $path_arr [0] . ".touclick";
		return true;
	}
	private function filterStr($str) {
		if (preg_match ( "/^[a-z0-9]+$/", $str ))
			return true;
		else
			return false;
	}
	private function requestGet($url) {
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$data = curl_exec ( $ch );
		curl_close ( $ch );
		return $data;
	}
}