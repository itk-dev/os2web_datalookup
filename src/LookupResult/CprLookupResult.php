<?php

namespace Drupal\os2web_datalookup\LookupResult;

class CprLookupResult {

  const CPR = 'cpr';
  const NAME = 'name';
  const STREET = 'street';
  const HOUSE_NR = 'houseNr';
  const FLOOR = 'floor';
  const APARTMENT_NR = 'apartmentNr';
  const POSTAL_CODE = 'postalCode';
  const CITY = 'city';
  const MUNICIPALITY_CODE = 'municipalityCode';
  const ADDRESS = 'address';
  const CO_NAME = 'coName';

  /**
   * Is request successful.
   *
   * @var bool
   */
  protected $successful = FALSE;

  /**
   * Status of the request.
   *
   * @var string
   */
  protected $errorMessage;

  /**
   * CPR
   *
   * @var string
   */
  protected $cpr;

  /**
   * Name of the person.
   *
   * @var string
   */
  protected $name;

  /**
   * Street of the person.
   *
   * @var string
   */
  protected $street;

  /**
   * Street house number of the person.
   *
   * @var string
   */
  protected $houseNr;

  /**
   * Floor number the person.
   *
   * @var string
   */
  protected $floor;

  /**
   * Apartment number of the person.
   *
   * @var string
   */
  protected $apartmentNr;

  /**
   * Postal code of the person.
   *
   * @var string
   */
  protected $postalCode;

  /**
   * City of the person.
   *
   * @var string
   */
  protected $city;

  /**
   * Municipality code of the person.
   *
   * @var string
   */
  protected $municipalityCode;

  /**
   * Address of the person.
   *
   * @var string
   */
  protected $address;

  /**
   * CO Name of the person.
   *
   * @var string
   */
  protected $coName;

  /**
   * Is name/address protected of the person.
   *
   * @var bool
   */
  protected $nameAddressProtected;

  /**
   * Array of children.
   *
   * @var array
   */
  protected $children = [];

  /**
   * @return bool
   */
  public function isSuccessful(): bool {
    return $this->successful;
  }

  /**
   * @param bool $successful
   */
  public function setSuccessful(bool $successful = TRUE): void {
    $this->successful = $successful;
  }

  /**
   * @return string
   */
  public function getErrorMessage(): string {
    return $this->errorMessage;
  }

  /**
   * @param string $errorMessage
   */
  public function setErrorMessage(string $errorMessage): void {
    $this->errorMessage = $errorMessage;
  }

  /**
   * @return string
   */
  public function getCpr(): string {
    return $this->cpr;
  }

  /**
   * @param string $cpr
   */
  public function setCpr(string $cpr): void {
    $this->cpr = $cpr;
  }

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName(string $name): void {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getStreet(): string {
    return $this->street;
  }

  /**
   * @param string $street
   */
  public function setStreet(string $street): void {
    $this->street = $street;
  }

  /**
   * @return string
   */
  public function getHouseNr(): string {
    return $this->houseNr;
  }

  /**
   * @param string $houseNr
   */
  public function setHouseNr(string $houseNr): void {
    $this->houseNr = $houseNr;
  }

  /**
   * @return string
   */
  public function getFloor(): string {
    return $this->floor;
  }

  /**
   * @param string $floor
   */
  public function setFloor(string $floor): void {
    $this->floor = $floor;
  }

  /**
   * @return string
   */
  public function getApartmentNr(): string {
    return $this->apartmentNr;
  }

  /**
   * @param string $apartmentNr
   */
  public function setApartmentNr(string $apartmentNr): void {
    $this->apartmentNr = $apartmentNr;
  }

  /**
   * @return string
   */
  public function getPostalCode(): string {
    return $this->postalCode;
  }

  /**
   * @param string $postalCode
   */
  public function setPostalCode(string $postalCode): void {
    $this->postalCode = $postalCode;
  }

  /**
   * @return string
   */
  public function getCity(): string {
    return $this->city;
  }

  /**
   * @param string $city
   */
  public function setCity(string $city): void {
    $this->city = $city;
  }

  /**
   * @return string
   */
  public function getMunicipalityCode(): string {
    return $this->municipalityCode;
  }

  /**
   * @param string $municipalityCode
   */
  public function setMunicipalityCode(string $municipalityCode): void {
    $this->municipalityCode = $municipalityCode;
  }

  /**
   * @return string
   */
  public function getAddress(): string {
    return $this->address;
  }

  /**
   * @param string $address
   */
  public function setAddress(string $address): void {
    $this->address = $address;
  }

  /**
   * @return string
   */
  public function getCoName(): string {
    return $this->coName;
  }

  /**
   * @param string $coName
   */
  public function setCoName(string $coName): void {
    $this->coName = $coName;
  }

  /**
   * @return bool
   */
  public function isNameAddressProtected(): bool {
    return $this->nameAddressProtected;
  }

  /**
   * @param bool $nameAddressProtected
   */
  public function setNameAddressProtected(bool $nameAddressProtected): void {
    $this->nameAddressProtected = $nameAddressProtected;
  }

  /**
   * Returns the children.
   *
   * @return array
   *   Children array as
   *   [
   *     0 => [
   *       'cpr' => xxxxx,
   *       'name' => full name,
   *     ],
   *     ...
   *   ]
   */
  public function getChildren(): array {
    return $this->children;
  }

  /**
   * Sets the children.
   *
   * @param array $children
   *   Children array as
   *   [
   *     0 => [
   *       'cpr' => xxxxx,
   *       'name' => full name,
   *     ],
   *     ...
   *   ]
   */
  public function setChildren(array $children): void {
    $this->children = $children;
  }

  /**
   * Returns the value of the provided field.
   *
   * @param $field
   *   Field name.
   *
   * @return mixed
   */
  public function getFieldValue($field) {
    if (property_exists($this, $field)) {
      return $this->{$field};
    }

    return '';
  }

}