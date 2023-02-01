<?php

namespace Drupal\os2web_datalookup\Plugin\os2web\DataLookup;

interface DataLookupInterfaceCompany extends DataLookupInterface {

  /**
   * Performs lookup for the provided param.
   *
   * @param string $param
   *   The param to query for.
   *
   * @return \Drupal\os2web_datalookup\LookupResult\CompanyLookupResult
   *   The company lookup Result.
   */
  public function lookup($param);

}
