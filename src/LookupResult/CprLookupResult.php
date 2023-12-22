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
   * Array of guardians.
   *
   * @var array
   */
  protected $guardians = [];

  /**
   * Is subscribe to digital post.
   *
   * @var bool
   */
  protected $digitalPostSubscribed = FALSE;

  /**
   * Is alive.
   *
   * @var bool
   */
  protected $alive = TRUE;

  /**
   * Is Danish citizen.
   *
   * @var bool
   */
  protected $citizen = TRUE;

  /**
   * Citizenship date obtained.
   *
   * @var \DateTime
   */
  protected \DateTime $citizenshipDate;

  /**
   * Date of birth.
   *
   * @var \DateTime
   */
  protected \DateTime $birthDate;

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
   * Returns the guardians.
   *
   * @return array
   *   Guardians array as
   *   [
   *     0 => [
   *       'cpr' => xxxxx,
   *       'type' => type of guardian,
   *     ],
   *     ...
   *   ]
   */
  public function getGuardians(): array {
    return $this->guardians;
  }

  /**
   * Sets the guardians.
   *
   * @param array $guardians
   *   Guardians array as
   *   [
   *     0 => [
   *       'cpr' => xxxxx,
   *       'type' => 1|2|3|4,
   *     ],
   *     ...
   *   ]
   */
  public function setGuardians(array $guardians): void {
    $this->guardians = $guardians;
  }

  /**
   * @param bool $digitalPostSubscribed
   */
  public function setDigitalPostSubscribed(bool $digitalPostSubscribed): void {
    $this->digitalPostSubscribed = $digitalPostSubscribed;
  }

  /**
   * @return bool
   */
  public function isDigitalPostSubscribed(): bool {
    return $this->digitalPostSubscribed;
  }

  /**
   * @return bool
   */
  public function isAlive(): bool {
    return $this->alive;
  }

  /**
   * @param bool $alive
   */
  public function setAlive(bool $alive): void {
    $this->alive = $alive;
  }

  /**
   * @return bool
   */
  public function isCitizen(): bool {
    return $this->citizen;
  }

  /**
   * @param bool $citizen
   */
  public function setCitizen(bool $citizen): void {
    $this->citizen = $citizen;
  }

  /**
   * @return \DateTime
   */
  public function getCitizenshipDate(): \DateTime {
    return $this->citizenshipDate;
  }

  /**
   * @param \DateTime $citizenshipDate
   */
  public function setCitizenshipDate(\DateTime $citizenshipDate): void {
    $this->citizenshipDate = $citizenshipDate;
  }

  /**
   * @return \DateTime
   */
  public function getBirthDate(): \DateTime {
    return $this->birthDate;
  }

  /**
   * @param \DateTime $birthDate
   */
  public function setBirthDate(\DateTime $birthDate): void {
    $this->birthDate = $birthDate;
  }

  /**
   * Checks if the provided CPR is in list of guardians.
   *
   * @param $cpr
   *   Cpr number.
   *
   * @return bool
   *   TRUE if person has provided CPR as guardian, FALSE otherwise.
   */
  public function hasGuardian($cpr): bool {
    foreach ($this->guardians as $guardian) {
      if ($guardian['cpr'] == $cpr) {
        return TRUE;
      }
    }

    return FALSE;
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
