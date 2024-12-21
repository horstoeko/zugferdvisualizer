<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer\translators;

use InvalidArgumentException;
use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerTranslatorContract;

/**
 * Class representing the default translator
 *
 * @category ZugferdVisualizer
 * @package  ZugferdVisualizer
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdvisualizer
 */
class ZugferdVisualizerDefaultTranslator implements ZugferdVisualizerTranslatorContract
{
    /**
     * Translations loaded
     *
     * @var array
     */
    private $translations = [];

    /**
     * The directories containing the translation file
     *
     * @var array
     */
    private $languageDirectories = [];

    /**
     * The currently selected language
     *
     * @var string
     */
    private $currentLanguage = 'en-US';

    /**
     * The fallback language
     *
     * @var string
     */
    private $fallbackLanguage = 'en-US';

    /**
     * The internal cache for translations
     *
     * @var array
     */
    private $translationsCache = [];

    /**
     * Constructor
     *
     * @param  array  $languageDirectories The directories where the language files are located
     * @param  string $currentLanguage     The primary language code to use
     * @param  string $fallbackLanguage    The fallback language code to use
     * @return void
     */
    public function __construct(array $languageDirectories = [], $currentLanguage = 'en-US', $fallbackLanguage = 'en-US')
    {
        $this->initialize($languageDirectories, $currentLanguage, $fallbackLanguage);
    }

    /**
     * Initialized the translator with language directories and languages.
     *
     * @param  array  $languageDirectories The directories where the language files are located
     * @param  string $currentLanguage     The primary language code to use
     * @param  string $fallbackLanguage    The fallback language code to use
     * @return void
     */
    public function initialize(array $languageDirectories = [], $currentLanguage = 'en-US', $fallbackLanguage = 'en-US')
    {
        foreach (!empty($languageDirectories) ?: [__DIR__ . '/../assets/translation'] as $languageDirectory) {
            $this->addLanguageDirectory($languageDirectory);
        }

        $this->setCurrentLanguage($currentLanguage);
        $this->setFallbackLanguage($fallbackLanguage);
    }

    /**
     * Add a directory that contains translation files.
     *
     * @param  string $languageDirectory
     * @return ZugferdVisualizerDefaultTranslator
     * @throws InvalidArgumentException
     */
    public function addLanguageDirectory(string $languageDirectory): ZugferdVisualizerDefaultTranslator
    {
        if (!is_dir($languageDirectory)) {
            throw new InvalidArgumentException("The specified directory does not exist: {$languageDirectory}");
        }

        if (in_array($languageDirectory, $this->languageDirectories)) {
            return $this;
        }

        $this->languageDirectories[] = rtrim($languageDirectory, DIRECTORY_SEPARATOR);

        $this->translationsCache = []; // Force reload

        return $this;
    }

    /**
     * Sets the current language.
     *
     * @param  string $language
     * @return ZugferdVisualizerDefaultTranslator
     * @throws InvalidArgumentException
     */
    public function setCurrentLanguage(string $language): ZugferdVisualizerDefaultTranslator
    {
        $language = $this->getNormalizedLanguageCode($language);

        if (!$this->getIsValidLanguage($language)) {
            throw new InvalidArgumentException("Invalid language format: {$language}. The format XX or XX-XX is expected.");
        }

        $this->currentLanguage = $language;

        return $this;
    }

    /**
     * Sets the fallback language.
     *
     * @param  string $language
     * @return ZugferdVisualizerDefaultTranslator
     * @throws InvalidArgumentException
     */
    public function setFallbackLanguage(string $language): ZugferdVisualizerDefaultTranslator
    {
        $language = $this->getNormalizedLanguageCode($language);

        if (!$this->getIsValidLanguage($language)) {
            throw new InvalidArgumentException("Invalid language format: {$language}. The format XX or XX-XX is expected.");
        }

        $this->fallbackLanguage = $language;

        return $this;
    }

    /**
     * Clear the list of observed language directories
     *
     * @return ZugferdVisualizerDefaultTranslator
     */
    public function clearLanguageDirectories(): ZugferdVisualizerDefaultTranslator
    {
        $this->languageDirectories = [];
        $this->translationsCache = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function translate(string $key, array $placeholders = [], ?string $domain = null): string
    {
        $this->loadTranslations();

        $translation = $this->getByPath($this->translations, $key, $domain);

        if ($translation === null) {
            return $key;
        }

        foreach ($placeholders as $placeholder => $placeholderValue) {
            $translation = str_replace(sprintf(":%s", $placeholder), $placeholderValue, $translation);
            $translation = str_replace(sprintf("{{%s}}", $placeholder), $placeholderValue, $translation);
        }

        return $translation;
    }

    /**
     * Translated a key without having optional place holders.
     * The key can be specified in the path.to.translation 'to search nested arrays.
     *
     * @param  string      $key
     * @param  null|string $domain
     * @return string
     */
    public function translateWithoutPlaceholders(string $key, ?string $domain = null): string
    {
        return $this->translate($key, [], $domain);
    }

    /**
     * Invites the translations for the current and the fallback language.
     *
     * @return void
     */
    private function loadTranslations(): void
    {
        $this->translations = $this->getMergedArray(
            $this->loadTranslationsForLanguage($this->fallbackLanguage),
            $this->loadTranslationsForLanguage($this->currentLanguage)
        );
    }

    /**
     * Loads the translation files for a specific language.
     *
     * @param  string $language
     * @return array
     */
    private function loadTranslationsForLanguage(string $language): array
    {
        if (isset($this->translationsCache[$language])) {
            return $this->translationsCache[$language];
        }

        $translations = [];

        foreach ($this->languageDirectories as $languageDirectory) {
            $filePath = $languageDirectory . DIRECTORY_SEPARATOR . "{$language}.php";

            if (file_exists($filePath)) {
                $fileTranslations = include $filePath;
                if (is_array($fileTranslations)) {
                    $translations = $this->getMergedArray($translations, $fileTranslations);
                }
            } else {
                $genericLanguage = explode('-', $language)[0];
                $genericFilePath = $languageDirectory . DIRECTORY_SEPARATOR . "{$genericLanguage}.php";
                if (file_exists($genericFilePath)) {
                    $fileTranslations = include $genericFilePath;
                    if (is_array($fileTranslations)) {
                        $translations = $this->getMergedArray($translations, $fileTranslations);
                    }
                }
            }
        }

        $this->translationsCache[$language] = $translations;

        return $translations;
    }

    /**
     * Merge translations
     *
     * @param  array $array1
     * @param  array $array2
     * @return array
     */
    private function getMergedArray(array $array1, array $array2)
    {
        foreach ($array2 as $key => $value) {
            if (array_key_exists($key, $array1) && is_array($array1[$key]) && is_array($value)) {
                $array1[$key] = $this->getMergedArray($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }

        return $array1;
    }

    /**
     * Get a normalized language code on the XX or XX-XX format.
     *
     * @param  string $language
     * @return string
     */
    private function getNormalizedLanguageCode(string $language): string
    {
        $languageParts = explode('-', $language);

        if (count($languageParts) === 2) {
            return strtolower($languageParts[0]) . '-' . strtoupper($languageParts[1]);
        }

        return strtolower($language);
    }

    /**
     * Validate a language
     *
     * @param  mixed $language
     * @return bool
     */
    private function getIsValidLanguage($language)
    {
        return preg_match('/^([a-z]{2})(-[A-Z]{2})?$/', $language) === 1;
    }

    /**
     * Get a value from a nested array based on a path (array of keys).
     *
     * @param  array        $array
     * @param  string|array $path
     * @param  string|null  $domain
     * @return string|null
     */
    private function getByPath(array $array, $path, ?string $domain = null): ?string
    {
        if (is_string($path)) {
            $path = explode('.', $path);
        }

        if ($domain) {
            array_unshift($path, $domain);
        }

        foreach ($path as $key) {
            if (!isset($array[$key])) {
                return null;
            }
            $array = $array[$key];
        }

        return $array;
    }
}
