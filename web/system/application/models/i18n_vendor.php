<?php

/**
 * i18n utility class, provides functionality to read from language files.
 */
class I18N_Vendor extends Model
{
    protected $I18N_FILE_BASE_NAME = 'system/application/language/ilios_strings_';
    protected $I18N_FILE_SUFFIX = '.properties';
    protected $I18N_DEFAULT_LOCALE = 'en_US';


    /**
     * TODO determine the PHP-land lifecycle of this class within CI; if it is long, investigate
     *              the best strategy for already-load-attempted properties files.
     * @var array
     */
    protected $_cachedLanguageMaps;

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::Model();

        $this->_cachedLanguageMaps = array();
    }

    /**
     * @todo add code docs
     */
    protected function _getAbsoluteFilePathForRelativeFile ($relative)
    {
        $rootDir = realpath('.');

        return realpath($rootDir . '/' . $relative);
    }

    /**
     * @todo add code docs
     */
    protected function _loadPropertiesFile ($lang = null)
    {
        $rhett = array();
        $propertiesFilePath = null;

        if ($lang != null) {
            $propertiesFilePath = $this->I18N_FILE_BASE_NAME . $lang . $this->I18N_FILE_SUFFIX;
            $propertiesFilePath = $this->_getAbsoluteFilePathForRelativeFile($propertiesFilePath);

            if (! file_exists($propertiesFilePath)) {
                $propertiesFilePath = null;
            }
        }

        if ($propertiesFilePath == null) {
            $propertiesFilePath = $this->I18N_FILE_BASE_NAME . $this->I18N_DEFAULT_LOCALE
                                                                        . $this->I18N_FILE_SUFFIX;
            $propertiesFilePath = $this->_getAbsoluteFilePathForRelativeFile($propertiesFilePath);
        }

        $needsMoreLineContent = false;
        $backslashStrLen = strlen("\\");

        $file = fopen($propertiesFilePath, "r")
                        or exit('Failed to open properties file:' . $propertiesFilePath);

        while (! feof($file)) {
            $line = fgets($file);

            if (empty($line) || ((! $needsMoreLineContent) && (strpos($line, "#") === 0))) {
                continue;
            }

            if (! $needsMoreLineContent) {
                $key = substr($line, 0, strpos($line, '='));
                $value = trim(substr($line, (strpos($line, '=') + 1), strlen($line)), "\r\n");
            }
            else {
                $value .= $line;
            }

            /* Check if ends with single '\' */
            if (strrpos($value, "\\") === (strlen($value) - $backslashStrLen)) {
                $value = substr($value, 0, (strlen($value) - 1)) . "\n";
                $needsMoreLineContent = true;
            }
            else {
                $needsMoreLineContent = false;
            }

            $rhett[$key] = $value;
        }

        fclose($file);

        return $rhett;
    }

    /**
     * @todo add code docs
     */
    protected function _getLanguageMapForLocale ($lang) {
        $rhett = null;

        if (isset($this->_cachedLanguageMaps[$lang])) {
            $rhett = $this->_cachedLanguageMaps[$lang];
        }
        else {
            $rhett = $this->_loadPropertiesFile($lang);

            $this->_cachedLanguageMaps[$lang] = $rhett;
        }

        return $rhett;
    }

    /**
     * @todo add code docs
     */
    public function dumpI18NStringsForLanguageAsJavascript ($lang, $javascriptArrayName, $contentSeparator)
    {
        $languageMap = $this->_getLanguageMapForLocale($lang);
        echo "var " . $javascriptArrayName . " = [];" . $contentSeparator;
        foreach ($languageMap as $key => $val) {
            echo $javascriptArrayName . "['" . $key . "'] = \"" . $val . "\";" . $contentSeparator;
        }
    }

    /**
     * Returns the text value for a given key from a language pack.
     * @param string $key the text key
     * @param string $lang the targetted language
     * @param boolean unescape if TRUE then escaped double quotes in the value will be unescaped
     * @return string the text
     */
    public function getI18NString ($key, $lang, $unescape = false)
    {
        $languageMap = $this->_getLanguageMapForLocale($lang);
        $value = array_key_exists($key, $languageMap) ? $languageMap[$key] : "?????";
        if ($unescape) {
            $value = str_replace('\"', '"', $value);
        }
        return $value;
    }

    /**
     * Wrapper around <code>getI18NString()</code>.
     * The only difference is that double quotes in the return-value get unescaped by default.
     * @see I18N_Vendor::getI18nString()
     * @param string $key the text key
     * @param string $lang the targetted language
     * @param boolean unescape if TRUE then escaped double quotes in the value will be unescaped
     * @return string the text
     */
    public function t ($key, $lang, $unescaped = true)
    {
        return $this->getI18NString($key, $lang, $unescaped);
    }
}
