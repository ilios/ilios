<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Language-map library.
 * Provides key-based lookup mechanism for translation strings.
 *
 * @todo Junk ALL of this in favour of a sane translation component [ST 2013/03/18]
 */
class LanguageMap
{
    /**
     * @var string
     */
    const I18N_FILE_BASE_NAME = 'application/language/ilios_strings_';

    /**
     * @var string
     */
    const I18N_FILE_SUFFIX = '.properties';

    /**
     * @var string
     */
    const I18N_DEFAULT_LOCALE = 'en_US';


    /**
     * @var array
     */
    protected $_cachedLanguageMaps;

    /**
     * @var string
     */
    protected $_lang;

    /**
     * Constructor.
     *
     * @param string $lang The language key. If none is given then the lang key is loaded from the app configuration.
     */
    public function __construct ($lang = null)
    {
        if (! isset($lang)) {
            // load the default locale key from the app. config
            $CI =& get_instance();
            $lang = $CI->config->item('ilios_default_lang_locale');
            // last ditch effort
            $lang = $lang ? $lang : self::I18N_DEFAULT_LOCALE;
        }

        $this->_lang = $lang;

        $this->_cachedLanguageMaps = array();
    }

    /**
     * Returns the absolute file path for a given relative file path.
     * @param string $relative the relative file path
     * @return string the absolute file path
     */
    protected function _getAbsoluteFilePathForRelativeFile ($relative)
    {
        $rootDir = realpath('.');

        return realpath($rootDir . '/' . $relative);
    }

    /**
     * Loads the language file for a given language key, then parses it into a lookup map.
     * @param string $lang the language key
     * @return array lookup map
     */
    protected function _loadPropertiesFile ($lang = null)
    {
        $rhett = array();
        $propertiesFilePath = null;

        if ($lang != null) {
            $propertiesFilePath = self::I18N_FILE_BASE_NAME . $lang . self::I18N_FILE_SUFFIX;
            $propertiesFilePath = $this->_getAbsoluteFilePathForRelativeFile($propertiesFilePath);

            if (! file_exists($propertiesFilePath)) {
                $propertiesFilePath = null;
            }
        }

        if ($propertiesFilePath == null) {
            $propertiesFilePath = self::I18N_FILE_BASE_NAME . self::I18N_DEFAULT_LOCALE
                . self::I18N_FILE_SUFFIX;
            $propertiesFilePath = $this->_getAbsoluteFilePathForRelativeFile($propertiesFilePath);
        }

        $needsMoreLineContent = false;
        $backslashStrLen = strlen("\\");

        $file = fopen($propertiesFilePath, "r")
            or exit('Failed to open properties file:' . $propertiesFilePath);

        $value = '';

        while (! feof($file)) {
            $line = fgets($file);

            if (empty($line) || ((! $needsMoreLineContent) && (strpos($line, "#") === 0))) {
                continue;
            }

            if (! $needsMoreLineContent) {
                $key = substr($line, 0, strpos($line, '='));
                $value = trim(substr($line, (strpos($line, '=') + 1), strlen($line)), "\r\n");
            } else {
                $value .= $line;
            }

            /* Check if ends with single '\' */
            if (strrpos($value, "\\") === (strlen($value) - $backslashStrLen)) {
                $value = substr($value, 0, (strlen($value) - 1)) . "\n";
                $needsMoreLineContent = true;
            } else {
                $needsMoreLineContent = false;
            }

            $rhett[$key] = $value;
        }

        fclose($file);

        return $rhett;
    }

    /**
     * Returns the lookup map for a given language.
     * @param string $lang the language key
     * @return array the language map
     */
    protected function _getLanguageMapForLocale ($lang)
    {
        $rhett = null;

        if (isset($this->_cachedLanguageMaps[$lang])) {
            $rhett = $this->_cachedLanguageMaps[$lang];
        } else {
            $rhett = $this->_loadPropertiesFile($lang);
            $this->_cachedLanguageMaps[$lang] = $rhett;
        }
        return $rhett;
    }

    /**
     * Prints the lookup map for a given language as global JavaScript variable.
     * @param string $javascriptArrayName the variable name
     * @param string $contentSeparator line separator
     */
    public function dumpI18NStringsForLanguageAsJavascript ($javascriptArrayName, $contentSeparator)
    {
        $languageMap = $this->_getLanguageMapForLocale($this->_lang);
        echo "var " . $javascriptArrayName . " = [];" . $contentSeparator;
        foreach ($languageMap as $key => $val) {
            echo $javascriptArrayName . "['" . $key . "'] = \"" . $val . "\";" . $contentSeparator;
        }
    }

    /**
     * Returns the text value for a given key from a language pack.
     * @param string $key the text key
     * @param boolean unescape if TRUE then escaped double quotes in the value will be unescaped
     * @return string the text
     */
    public function getI18NString ($key, $unescape = false)
    {
        $languageMap = $this->_getLanguageMapForLocale($this->_lang);
        $value = array_key_exists($key, $languageMap) ? $languageMap[$key] : "?????";
        if ($unescape) {
            $value = str_replace('\"', '"', $value);
        }
        return $value;
    }

    /**
     * Wrapper around <code>getI18NString()</code>.
     * The only difference is that double quotes in the return-value get unescaped by default.
     * @see LanguageMap::getI18nString()
     * @param string $key the text key
     * @param boolean unescape if TRUE then escaped double quotes in the value will be unescaped
     * @return string the text
     */
    public function t ($key, $unescape = true)
    {
        return $this->getI18NString($key, $this->_lang, $unescape);
    }
}
