<?php

namespace Shikaemon\ImageManager\Libraries\Models\Enum;

/**
 * Class Geo
 * @package Shikaemon\ImageManager\Libraries\Models\Enum
 * @property $longitudeReference
 * @property $latitudeReference
 * @property $longitudeDegree
 * @property $longitudeMinute
 * @property $longitudeSecond
 * @property $longitude
 * @property $latitudeDegree
 * @property $latitudeMinute
 * @property $latitudeSecond
 * @property $latitude
 */
class Geo
{
    const KEY_GPS_LONGITUDE_REF = 'GPSLongitudeRef';
    const KEY_GPS_LONGITUDE = 'GPSLongitude';
    const KEY_GPS_LATITUDE_REF = 'GPSLatitudeRef';
    const KEY_GPS_LATITUDE = 'GPSLatitude';
    const SOUTH = 's';
    const WEST = 'w';

    private $longitudeReference;
    private $longitudeDegree;
    private $longitudeMinute;
    private $longitudeSecond;
    private $longitude;
    private $latitudeReference;
    private $latitudeDegree;
    private $latitudeMinute;
    private $latitudeSecond;
    private $latitude;

    /**
     * Geo constructor.
     * @param null $exifArray
     */
    public function __construct($exifArray = null)
    {
        if (!isset($exifArray)) {
            return;
        }
        $this->longitudeReference = $exifArray[self::KEY_GPS_LONGITUDE_REF] ?? null;
        $this->latitudeReference = $exifArray[self::KEY_GPS_LATITUDE_REF] ?? null;

        $this->longitudeDegree = $exifArray[self::KEY_GPS_LONGITUDE][0] ?? 0;
        $this->longitudeMinute = $exifArray[self::KEY_GPS_LONGITUDE][1] ?? 0;
        $this->longitudeSecond = $exifArray[self::KEY_GPS_LONGITUDE][2] ?? 0;
        $this->latitudeDegree = $exifArray[self::KEY_GPS_LATITUDE][0] ?? 0;
        $this->latitudeMinute = $exifArray[self::KEY_GPS_LATITUDE][1] ?? 0;
        $this->latitudeSecond = $exifArray[self::KEY_GPS_LATITUDE][2] ?? 0;
        $this->convertLongitude();
        $this->convertLatitude();
    }

    /**
     * @return float|int|null
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return float|int|null
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return float|int
     */
    private function convertLongitude()
    {
        $data = $this->convert_float($this->convert_float($this->longitudeDegree) + ($this->convert_float($this->longitudeMinute) / 60) + ($this->convert_float($this->longitudeSecond) / 3600));
        return $this->latitude = ($this->longitudeReference == self::WEST) ? ($data * -1) : $data;
    }

    /**
     * @return float|int
     */
    private function convertLatitude()
    {
        $data = $this->convert_float($this->convert_float($this->latitudeDegree) + ($this->convert_float($this->latitudeMinute) / 60) + ($this->convert_float($this->latitudeSecond) / 3600));
        return $this->longitude = ($this->latitudeReference == self::SOUTH) ? ($data * -1) : $data;
    }

    /**
     * @param $str
     * @return float|int
     */
    private function convert_float($str)
    {
        $val = explode('/', $str);
        return (isset($val[1])) ? $val[0] / $val[1] : $str;
    }
}
