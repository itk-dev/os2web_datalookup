<?php

namespace Drupal\os2web_datalookup\Plugin\os2web\DataLookup;

interface DataLookupInterfaceCvr extends DataLookupInterface {

  /**
   * Performs lookup for the provided CVR.
   *
   * @param string $cvr
   *   The CVR number to query for.
   *
   * @return \Drupal\os2web_datalookup\LookupResult\CvrLookupResult
   *   The CVR lookup Result.
   */
  public function lookup($cvr);

}
