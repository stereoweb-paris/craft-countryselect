<?php
/**
 * Country Select plugin for Craft CMS 3.x
 *
 * Country select field type.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\countryselect\fields;

use lukeyouell\countryselect\CountrySelect;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\fields\data\OptionData;
use craft\fields\data\MultiOptionsFieldData;
use craft\fields\data\SingleOptionFieldData;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * @author    Luke Youell
 * @package   CountrySelect
 * @since     1.0.0
 */
class CountrySelectBaseOptionsField extends Field
{
    // Properties
    // =========================================================================

    /**
     * @var array|null The available options
     */
    public $options;

    /**
     * @var bool Whether the field should support multiple selections
     */
    protected $multi = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->options = $this->translatedOptions();
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        if ($this->multi) {
            // See how much data we could possibly be saving if everything was selected.
            $length = 0;

            if ($this->options) {
                foreach ($this->options as $option) {
                    if (!empty($option['value'])) {
                        // +3 because it will be json encoded. Includes the surrounding quotes and comma.
                        $length += strlen($option['value']) + 3;
                    }
                }
            }

            // Add +2 for the outer brackets and -1 for the last comma.
            return Db::getTextualColumnTypeByContentLength($length + 1);
        }

        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if ($value instanceof MultiOptionsFieldData || $value instanceof SingleOptionFieldData) {
            return $value;
        }

        if (is_string($value)) {
            $value = Json::decodeIfJson($value);
        }

        // Normalize to an array
        $selectedValues = (array) $value;

        if ($this->multi) {
            // Convert the value to a MultiOptionsFieldData object
            $options = [];
            // var_dump($selectedValues);
            if(count($selectedValues) > 0) {
              foreach ($selectedValues as $val) {
                if(is_array($val)) {
                  $label = $val['label'];
                  $val = $val['value'];
                } else {
                  $label = $this->optionLabel($val);
                }
                $options[] = new OptionData($label, $val, true);
              }
            }
            $value = new MultiOptionsFieldData($options);
        } else {
            // Convert the value to a SingleOptionFieldData object
            $value = reset($selectedValues) ?: null;
            $label = $this->optionLabel($value);
            $value = new SingleOptionFieldData($label, $value, true);
        }

        $options = [];

        if ($this->options) {
            foreach ($this->options as $option) {
                $selected = in_array($option['value'], $selectedValues, true);
                $options[] = new OptionData($option['label'], $option['value'], $selected);
            }
        }

        $value->setOptions($options);

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    // Protected Methods
    // =========================================================================

    /**
     * Returns an option's label by its value.
     *
     * @param string|null $value
     * @return string|null
     */
    protected function optionLabel(string $value = null)
    {
        if ($this->options) {
            foreach ($this->options as $option) {
                if ($option['value'] == $value) {
                    return $option['label'];
                }
            }
        }

        return $value;
    }

    /**
     * @return array
     */
     protected function translatedOptions()
     {
         $countries = [
             ['value' => 'AD', 'label' => Craft::t('app', 'Andorra')],
             ['value' => 'AE', 'label' => Craft::t('app', 'United Arab Emirates')],
             ['value' => 'AF', 'label' => Craft::t('app', 'Afghanistan')],
             ['value' => 'AG', 'label' => Craft::t('app', 'Antigua and Barbuda')],
             ['value' => 'AI', 'label' => Craft::t('app', 'Anguilla')],
             ['value' => 'AL', 'label' => Craft::t('app', 'Albania')],
             ['value' => 'AM', 'label' => Craft::t('app', 'Armenia')],
             ['value' => 'AO', 'label' => Craft::t('app', 'Angola')],
             ['value' => 'AP', 'label' => Craft::t('app', 'Asia/Pacific Region')],
             ['value' => 'AQ', 'label' => Craft::t('app', 'Antarctica')],
             ['value' => 'AR', 'label' => Craft::t('app', 'Argentina')],
             ['value' => 'AS', 'label' => Craft::t('app', 'American Samoa')],
             ['value' => 'AT', 'label' => Craft::t('app', 'Austria')],
             ['value' => 'AU', 'label' => Craft::t('app', 'Australia')],
             ['value' => 'AW', 'label' => Craft::t('app', 'Aruba')],
             ['value' => 'AX', 'label' => Craft::t('app', 'Aland Islands')],
             ['value' => 'AZ', 'label' => Craft::t('app', 'Azerbaijan')],
             ['value' => 'BA', 'label' => Craft::t('app', 'Bosnia and Herzegovina')],
             ['value' => 'BB', 'label' => Craft::t('app', 'Barbados')],
             ['value' => 'BD', 'label' => Craft::t('app', 'Bangladesh')],
             ['value' => 'BE', 'label' => Craft::t('app', 'Belgium')],
             ['value' => 'BF', 'label' => Craft::t('app', 'Burkina Faso')],
             ['value' => 'BG', 'label' => Craft::t('app', 'Bulgaria')],
             ['value' => 'BH', 'label' => Craft::t('app', 'Bahrain')],
             ['value' => 'BI', 'label' => Craft::t('app', 'Burundi')],
             ['value' => 'BJ', 'label' => Craft::t('app', 'Benin')],
             ['value' => 'BL', 'label' => Craft::t('app', 'Saint Bartelemey')],
             ['value' => 'BM', 'label' => Craft::t('app', 'Bermuda')],
             ['value' => 'BN', 'label' => Craft::t('app', 'Brunei Darussalam')],
             ['value' => 'BO', 'label' => Craft::t('app', 'Bolivia')],
             ['value' => 'BQ', 'label' => Craft::t('app', 'Bonaire, Saint Eustatius and Saba')],
             ['value' => 'BR', 'label' => Craft::t('app', 'Brazil')],
             ['value' => 'BS', 'label' => Craft::t('app', 'Bahamas')],
             ['value' => 'BT', 'label' => Craft::t('app', 'Bhutan')],
             ['value' => 'BV', 'label' => Craft::t('app', 'Bouvet Island')],
             ['value' => 'BW', 'label' => Craft::t('app', 'Botswana')],
             ['value' => 'BY', 'label' => Craft::t('app', 'Belarus')],
             ['value' => 'BZ', 'label' => Craft::t('app', 'Belize')],
             ['value' => 'CA', 'label' => Craft::t('app', 'Canada')],
             ['value' => 'CC', 'label' => Craft::t('app', 'Cocos (Keeling) Islands')],
             ['value' => 'CD', 'label' => Craft::t('app', 'Congo, The Democratic Republic of the')],
             ['value' => 'CF', 'label' => Craft::t('app', 'Central African Republic')],
             ['value' => 'CG', 'label' => Craft::t('app', 'Congo')],
             ['value' => 'CH', 'label' => Craft::t('app', 'Switzerland')],
             ['value' => 'CI', 'label' => Craft::t('app', 'Cote d\'Ivoire')],
             ['value' => 'CK', 'label' => Craft::t('app', 'Cook Islands')],
             ['value' => 'CL', 'label' => Craft::t('app', 'Chile')],
             ['value' => 'CM', 'label' => Craft::t('app', 'Cameroon')],
             ['value' => 'CN', 'label' => Craft::t('app', 'China')],
             ['value' => 'CO', 'label' => Craft::t('app', 'Colombia')],
             ['value' => 'CR', 'label' => Craft::t('app', 'Costa Rica')],
             ['value' => 'CU', 'label' => Craft::t('app', 'Cuba')],
             ['value' => 'CV', 'label' => Craft::t('app', 'Cape Verde')],
             ['value' => 'CW', 'label' => Craft::t('app', 'Curacao')],
             ['value' => 'CX', 'label' => Craft::t('app', 'Christmas Island')],
             ['value' => 'CY', 'label' => Craft::t('app', 'Cyprus')],
             ['value' => 'CZ', 'label' => Craft::t('app', 'Czech Republic')],
             ['value' => 'DE', 'label' => Craft::t('app', 'Germany')],
             ['value' => 'DJ', 'label' => Craft::t('app', 'Djibouti')],
             ['value' => 'DK', 'label' => Craft::t('app', 'Denmark')],
             ['value' => 'DM', 'label' => Craft::t('app', 'Dominica')],
             ['value' => 'DO', 'label' => Craft::t('app', 'Dominican Republic')],
             ['value' => 'DZ', 'label' => Craft::t('app', 'Algeria')],
             ['value' => 'EC', 'label' => Craft::t('app', 'Ecuador')],
             ['value' => 'EE', 'label' => Craft::t('app', 'Estonia')],
             ['value' => 'EG', 'label' => Craft::t('app', 'Egypt')],
             ['value' => 'EH', 'label' => Craft::t('app', 'Western Sahara')],
             ['value' => 'ER', 'label' => Craft::t('app', 'Eritrea')],
             ['value' => 'ES', 'label' => Craft::t('app', 'Spain')],
             ['value' => 'ET', 'label' => Craft::t('app', 'Ethiopia')],
             ['value' => 'EU', 'label' => Craft::t('app', 'Europe')],
             ['value' => 'FI', 'label' => Craft::t('app', 'Finland')],
             ['value' => 'FJ', 'label' => Craft::t('app', 'Fiji')],
             ['value' => 'FK', 'label' => Craft::t('app', 'Falkland Islands (Malvinas)')],
             ['value' => 'FM', 'label' => Craft::t('app', 'Micronesia, Federated States of')],
             ['value' => 'FO', 'label' => Craft::t('app', 'Faroe Islands')],
             ['value' => 'FR', 'label' => Craft::t('app', 'France')],
             ['value' => 'GA', 'label' => Craft::t('app', 'Gabon')],
             ['value' => 'GB', 'label' => Craft::t('app', 'United Kingdom')],
             ['value' => 'GD', 'label' => Craft::t('app', 'Grenada')],
             ['value' => 'GE', 'label' => Craft::t('app', 'Georgia')],
             ['value' => 'GF', 'label' => Craft::t('app', 'French Guiana')],
             ['value' => 'GG', 'label' => Craft::t('app', 'Guernsey')],
             ['value' => 'GH', 'label' => Craft::t('app', 'Ghana')],
             ['value' => 'GI', 'label' => Craft::t('app', 'Gibraltar')],
             ['value' => 'GL', 'label' => Craft::t('app', 'Greenland')],
             ['value' => 'GM', 'label' => Craft::t('app', 'Gambia')],
             ['value' => 'GN', 'label' => Craft::t('app', 'Guinea')],
             ['value' => 'GP', 'label' => Craft::t('app', 'Guadeloupe')],
             ['value' => 'GQ', 'label' => Craft::t('app', 'Equatorial Guinea')],
             ['value' => 'GR', 'label' => Craft::t('app', 'Greece')],
             ['value' => 'GS', 'label' => Craft::t('app', 'South Georgia and the South Sandwich Islands')],
             ['value' => 'GT', 'label' => Craft::t('app', 'Guatemala')],
             ['value' => 'GU', 'label' => Craft::t('app', 'Guam')],
             ['value' => 'GW', 'label' => Craft::t('app', 'Guinea-Bissau')],
             ['value' => 'GY', 'label' => Craft::t('app', 'Guyana')],
             ['value' => 'HK', 'label' => Craft::t('app', 'Hong Kong')],
             ['value' => 'HM', 'label' => Craft::t('app', 'Heard Island and McDonald Islands')],
             ['value' => 'HN', 'label' => Craft::t('app', 'Honduras')],
             ['value' => 'HR', 'label' => Craft::t('app', 'Croatia')],
             ['value' => 'HT', 'label' => Craft::t('app', 'Haiti')],
             ['value' => 'HU', 'label' => Craft::t('app', 'Hungary')],
             ['value' => 'ID', 'label' => Craft::t('app', 'Indonesia')],
             ['value' => 'IE', 'label' => Craft::t('app', 'Ireland')],
             ['value' => 'IL', 'label' => Craft::t('app', 'Israel')],
             ['value' => 'IM', 'label' => Craft::t('app', 'Isle of Man')],
             ['value' => 'IN', 'label' => Craft::t('app', 'India')],
             ['value' => 'IO', 'label' => Craft::t('app', 'British Indian Ocean Territory')],
             ['value' => 'IQ', 'label' => Craft::t('app', 'Iraq')],
             ['value' => 'IR', 'label' => Craft::t('app', 'Iran, Islamic Republic of')],
             ['value' => 'IS', 'label' => Craft::t('app', 'Iceland')],
             ['value' => 'IT', 'label' => Craft::t('app', 'Italy')],
             ['value' => 'JE', 'label' => Craft::t('app', 'Jersey')],
             ['value' => 'JM', 'label' => Craft::t('app', 'Jamaica')],
             ['value' => 'JO', 'label' => Craft::t('app', 'Jordan')],
             ['value' => 'JP', 'label' => Craft::t('app', 'Japan')],
             ['value' => 'KE', 'label' => Craft::t('app', 'Kenya')],
             ['value' => 'KG', 'label' => Craft::t('app', 'Kyrgyzstan')],
             ['value' => 'KH', 'label' => Craft::t('app', 'Cambodia')],
             ['value' => 'KI', 'label' => Craft::t('app', 'Kiribati')],
             ['value' => 'KM', 'label' => Craft::t('app', 'Comoros')],
             ['value' => 'KN', 'label' => Craft::t('app', 'Saint Kitts and Nevis')],
             ['value' => 'KP', 'label' => Craft::t('app', 'Korea, Democratic People\'s Republic of')],
             ['value' => 'KR', 'label' => Craft::t('app', 'Korea, Republic of')],
             ['value' => 'KW', 'label' => Craft::t('app', 'Kuwait')],
             ['value' => 'KY', 'label' => Craft::t('app', 'Cayman Islands')],
             ['value' => 'KZ', 'label' => Craft::t('app', 'Kazakhstan')],
             ['value' => 'LA', 'label' => Craft::t('app', 'Lao People\'s Democratic Republic')],
             ['value' => 'LB', 'label' => Craft::t('app', 'Lebanon')],
             ['value' => 'LC', 'label' => Craft::t('app', 'Saint Lucia')],
             ['value' => 'LI', 'label' => Craft::t('app', 'Liechtenstein')],
             ['value' => 'LK', 'label' => Craft::t('app', 'Sri Lanka')],
             ['value' => 'LR', 'label' => Craft::t('app', 'Liberia')],
             ['value' => 'LS', 'label' => Craft::t('app', 'Lesotho')],
             ['value' => 'LT', 'label' => Craft::t('app', 'Lithuania')],
             ['value' => 'LU', 'label' => Craft::t('app', 'Luxembourg')],
             ['value' => 'LV', 'label' => Craft::t('app', 'Latvia')],
             ['value' => 'LY', 'label' => Craft::t('app', 'Libyan Arab Jamahiriya')],
             ['value' => 'MA', 'label' => Craft::t('app', 'Morocco')],
             ['value' => 'MC', 'label' => Craft::t('app', 'Monaco')],
             ['value' => 'MD', 'label' => Craft::t('app', 'Moldova, Republic of')],
             ['value' => 'ME', 'label' => Craft::t('app', 'Montenegro')],
             ['value' => 'MF', 'label' => Craft::t('app', 'Saint Martin')],
             ['value' => 'MG', 'label' => Craft::t('app', 'Madagascar')],
             ['value' => 'MH', 'label' => Craft::t('app', 'Marshall Islands')],
             ['value' => 'MK', 'label' => Craft::t('app', 'Macedonia')],
             ['value' => 'ML', 'label' => Craft::t('app', 'Mali')],
             ['value' => 'MM', 'label' => Craft::t('app', 'Myanmar')],
             ['value' => 'MN', 'label' => Craft::t('app', 'Mongolia')],
             ['value' => 'MO', 'label' => Craft::t('app', 'Macao')],
             ['value' => 'MP', 'label' => Craft::t('app', 'Northern Mariana Islands')],
             ['value' => 'MQ', 'label' => Craft::t('app', 'Martinique')],
             ['value' => 'MR', 'label' => Craft::t('app', 'Mauritania')],
             ['value' => 'MS', 'label' => Craft::t('app', 'Montserrat')],
             ['value' => 'MT', 'label' => Craft::t('app', 'Malta')],
             ['value' => 'MU', 'label' => Craft::t('app', 'Mauritius')],
             ['value' => 'MV', 'label' => Craft::t('app', 'Maldives')],
             ['value' => 'MW', 'label' => Craft::t('app', 'Malawi')],
             ['value' => 'MX', 'label' => Craft::t('app', 'Mexico')],
             ['value' => 'MY', 'label' => Craft::t('app', 'Malaysia')],
             ['value' => 'MZ', 'label' => Craft::t('app', 'Mozambique')],
             ['value' => 'NA', 'label' => Craft::t('app', 'Namibia')],
             ['value' => 'NC', 'label' => Craft::t('app', 'New Caledonia')],
             ['value' => 'NE', 'label' => Craft::t('app', 'Niger')],
             ['value' => 'NF', 'label' => Craft::t('app', 'Norfolk Island')],
             ['value' => 'NG', 'label' => Craft::t('app', 'Nigeria')],
             ['value' => 'NI', 'label' => Craft::t('app', 'Nicaragua')],
             ['value' => 'NL', 'label' => Craft::t('app', 'Netherlands')],
             ['value' => 'NO', 'label' => Craft::t('app', 'Norway')],
             ['value' => 'NP', 'label' => Craft::t('app', 'Nepal')],
             ['value' => 'NR', 'label' => Craft::t('app', 'Nauru')],
             ['value' => 'NU', 'label' => Craft::t('app', 'Niue')],
             ['value' => 'NZ', 'label' => Craft::t('app', 'New Zealand')],
             ['value' => 'OM', 'label' => Craft::t('app', 'Oman')],
             ['value' => 'PA', 'label' => Craft::t('app', 'Panama')],
             ['value' => 'PE', 'label' => Craft::t('app', 'Peru')],
             ['value' => 'PF', 'label' => Craft::t('app', 'French Polynesia')],
             ['value' => 'PG', 'label' => Craft::t('app', 'Papua New Guinea')],
             ['value' => 'PH', 'label' => Craft::t('app', 'Philippines')],
             ['value' => 'PK', 'label' => Craft::t('app', 'Pakistan')],
             ['value' => 'PL', 'label' => Craft::t('app', 'Poland')],
             ['value' => 'PM', 'label' => Craft::t('app', 'Saint Pierre and Miquelon')],
             ['value' => 'PN', 'label' => Craft::t('app', 'Pitcairn')],
             ['value' => 'PR', 'label' => Craft::t('app', 'Puerto Rico')],
             ['value' => 'PS', 'label' => Craft::t('app', 'Palestinian Territory')],
             ['value' => 'PT', 'label' => Craft::t('app', 'Portugal')],
             ['value' => 'PW', 'label' => Craft::t('app', 'Palau')],
             ['value' => 'PY', 'label' => Craft::t('app', 'Paraguay')],
             ['value' => 'QA', 'label' => Craft::t('app', 'Qatar')],
             ['value' => 'RE', 'label' => Craft::t('app', 'Reunion')],
             ['value' => 'RO', 'label' => Craft::t('app', 'Romania')],
             ['value' => 'RS', 'label' => Craft::t('app', 'Serbia')],
             ['value' => 'RU', 'label' => Craft::t('app', 'Russian Federation')],
             ['value' => 'RW', 'label' => Craft::t('app', 'Rwanda')],
             ['value' => 'SA', 'label' => Craft::t('app', 'Saudi Arabia')],
             ['value' => 'SB', 'label' => Craft::t('app', 'Solomon Islands')],
             ['value' => 'SC', 'label' => Craft::t('app', 'Seychelles')],
             ['value' => 'SD', 'label' => Craft::t('app', 'Sudan')],
             ['value' => 'SE', 'label' => Craft::t('app', 'Sweden')],
             ['value' => 'SG', 'label' => Craft::t('app', 'Singapore')],
             ['value' => 'SH', 'label' => Craft::t('app', 'Saint Helena')],
             ['value' => 'SI', 'label' => Craft::t('app', 'Slovenia')],
             ['value' => 'SJ', 'label' => Craft::t('app', 'Svalbard and Jan Mayen')],
             ['value' => 'SK', 'label' => Craft::t('app', 'Slovakia')],
             ['value' => 'SL', 'label' => Craft::t('app', 'Sierra Leone')],
             ['value' => 'SM', 'label' => Craft::t('app', 'San Marino')],
             ['value' => 'SN', 'label' => Craft::t('app', 'Senegal')],
             ['value' => 'SO', 'label' => Craft::t('app', 'Somalia')],
             ['value' => 'SR', 'label' => Craft::t('app', 'Suriname')],
             ['value' => 'SS', 'label' => Craft::t('app', 'South Sudan')],
             ['value' => 'ST', 'label' => Craft::t('app', 'Sao Tome and Principe')],
             ['value' => 'SV', 'label' => Craft::t('app', 'El Salvador')],
             ['value' => 'SX', 'label' => Craft::t('app', 'Sint Maarten')],
             ['value' => 'SY', 'label' => Craft::t('app', 'Syrian Arab Republic')],
             ['value' => 'SZ', 'label' => Craft::t('app', 'Swaziland')],
             ['value' => 'TC', 'label' => Craft::t('app', 'Turks and Caicos Islands')],
             ['value' => 'TD', 'label' => Craft::t('app', 'Chad')],
             ['value' => 'TF', 'label' => Craft::t('app', 'French Southern Territories')],
             ['value' => 'TG', 'label' => Craft::t('app', 'Togo')],
             ['value' => 'TH', 'label' => Craft::t('app', 'Thailand')],
             ['value' => 'TJ', 'label' => Craft::t('app', 'Tajikistan')],
             ['value' => 'TK', 'label' => Craft::t('app', 'Tokelau')],
             ['value' => 'TL', 'label' => Craft::t('app', 'Timor-Leste')],
             ['value' => 'TM', 'label' => Craft::t('app', 'Turkmenistan')],
             ['value' => 'TN', 'label' => Craft::t('app', 'Tunisia')],
             ['value' => 'TO', 'label' => Craft::t('app', 'Tonga')],
             ['value' => 'TR', 'label' => Craft::t('app', 'Turkey')],
             ['value' => 'TT', 'label' => Craft::t('app', 'Trinidad and Tobago')],
             ['value' => 'TV', 'label' => Craft::t('app', 'Tuvalu')],
             ['value' => 'TW', 'label' => Craft::t('app', 'Taiwan')],
             ['value' => 'TZ', 'label' => Craft::t('app', 'Tanzania, United Republic of')],
             ['value' => 'UA', 'label' => Craft::t('app', 'Ukraine')],
             ['value' => 'UG', 'label' => Craft::t('app', 'Uganda')],
             ['value' => 'UM', 'label' => Craft::t('app', 'United States Minor Outlying Islands')],
             ['value' => 'US', 'label' => Craft::t('app', 'United States')],
             ['value' => 'UY', 'label' => Craft::t('app', 'Uruguay')],
             ['value' => 'UZ', 'label' => Craft::t('app', 'Uzbekistan')],
             ['value' => 'VA', 'label' => Craft::t('app', 'Holy See (Vatican City State)')],
             ['value' => 'VC', 'label' => Craft::t('app', 'Saint Vincent and the Grenadines')],
             ['value' => 'VE', 'label' => Craft::t('app', 'Venezuela')],
             ['value' => 'VG', 'label' => Craft::t('app', 'Virgin Islands, British')],
             ['value' => 'VI', 'label' => Craft::t('app', 'Virgin Islands, U.S.')],
             ['value' => 'VN', 'label' => Craft::t('app', 'Vietnam')],
             ['value' => 'VU', 'label' => Craft::t('app', 'Vanuatu')],
             ['value' => 'WF', 'label' => Craft::t('app', 'Wallis and Futuna')],
             ['value' => 'WS', 'label' => Craft::t('app', 'Samoa')],
             ['value' => 'YE', 'label' => Craft::t('app', 'Yemen')],
             ['value' => 'YT', 'label' => Craft::t('app', 'Mayotte')],
             ['value' => 'ZA', 'label' => Craft::t('app', 'South Africa')],
             ['value' => 'ZM', 'label' => Craft::t('app', 'Zambia')],
             ['value' => 'ZW', 'label' => Craft::t('app', 'Zimbabwe')],
         ];

         // Sort countries by label
         usort($countries, function($a, $b) {
             return strcasecmp($a['label'], $b['label']);
         });

         return $countries;
     }
}
