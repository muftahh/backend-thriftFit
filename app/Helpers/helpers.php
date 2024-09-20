<?php

if (! function_exists('moneyFormat')) {
  /**
   * GlobalFunction
   * untuk negganti format angka 
   * atau harga ke rupiah
   * 
   * @return string
   */
  function moneyFormat($str) {
    return 'Rp. '. number_format($str, '0', '', '.');
    // number_format(angka, angka_di_belakang_koma, pemisah_desimal, pemisah_ribuan);
  }
}