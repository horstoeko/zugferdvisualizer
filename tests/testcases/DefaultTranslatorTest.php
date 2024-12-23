<?php

namespace horstoeko\zugferdvisualizer\tests\testcases;

use InvalidArgumentException;
use horstoeko\zugferdvisualizer\tests\TestCase;
use horstoeko\zugferdvisualizer\translators\ZugferdVisualizerDefaultTranslator;

class DefaultTranslatorTest extends TestCase
{
    public function testInitialize(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();

        $this->assertEmpty($this->getPrivatePropertyFromObject($translator, 'translations')->getValue($translator));
        $this->assertNotEmpty($this->getPrivatePropertyFromObject($translator, 'languageDirectories')->getValue($translator));
        $this->assertCount(1, $this->getPrivatePropertyFromObject($translator, 'languageDirectories')->getValue($translator));
        $this->assertEquals('en-US', $this->getPrivatePropertyFromObject($translator, 'currentLanguage')->getValue($translator));
        $this->assertEquals('en-US', $this->getPrivatePropertyFromObject($translator, 'fallbackLanguage')->getValue($translator));
        $this->assertEmpty($this->getPrivatePropertyFromObject($translator, 'translationsCache')->getValue($translator));
    }

    public function testAddExistingLanguageDirectory(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertNotEmpty($this->getPrivatePropertyFromObject($translator, 'languageDirectories')->getValue($translator));
        $this->assertCount(2, $this->getPrivatePropertyFromObject($translator, 'languageDirectories')->getValue($translator));
    }

    public function testAddNotExistingLanguageDirectory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/The specified directory does not exist/');

        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->addLanguageDirectory(__DIR__ . '/../translations');
    }

    public function testSetValidCurrentLanguage(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->setCurrentLanguage('de-DE');

        $this->assertEquals('de-DE', $this->getPrivatePropertyFromObject($translator, 'currentLanguage')->getValue($translator));
    }

    public function testSetInvalidCurrentLanguage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid language format: invalid. The format XX or XX-XX is expected.');

        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->setCurrentLanguage('invalid');
    }

    public function testSetValidFallbackLanguage(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->setFallbackLanguage('de-DE');

        $this->assertEquals('de-DE', $this->getPrivatePropertyFromObject($translator, 'fallbackLanguage')->getValue($translator));
    }

    public function testSetInvalidFallbackLanguage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid language format: invalid. The format XX or XX-XX is expected.');

        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->setFallbackLanguage('invalid');
    }

    public function testTranslateWithTranslateableKey(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('Piece', $translator->translate('H87', [], 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de-DE');

        $this->assertEquals('St.', $translator->translate('H87', [], 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);
    }

    public function testTranslateWithNonTranslateableKey(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('C62', $translator->translate('C62', [], 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de-DE');

        $this->assertEquals('C62', $translator->translate('C62', [], 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);
    }

    public function testTranslateWithTranslateableKeyNoDomain(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('Piece', $translator->translate('unitcodes.H87', []));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de-DE');

        $this->assertEquals('St.', $translator->translate('unitcodes.H87', []));
        $this->assertTranslationCacheNotEmpty($translator);
    }

    public function testTranslateWithNonTranslateableKeyNoDomain(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('unitcodes.C62', $translator->translate('unitcodes.C62', []));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de-DE');

        $this->assertEquals('unitcodes.C62', $translator->translate('unitcodes.C62', []));
        $this->assertTranslationCacheNotEmpty($translator);
    }

    public function testTranslateWithTranslateableKeyAndPlaceHolders(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('greeting', $translator->translate('greeting', ['name' => 'John Doe'], 'general'));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de-DE');

        $this->assertEquals('Hello John Doe', $translator->translate('greeting', ['name' => 'John Doe'], 'general'));
        $this->assertTranslationCacheNotEmpty($translator);

        $this->assertEquals('Hello John Doe', $translator->translate('greeting2', ['name' => 'John Doe'], 'general'));
        $this->assertTranslationCacheNotEmpty($translator);
    }

    public function testTranslateWithTranslateableKeyAndPlaceHoldersNotPresentPlaceholder(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('greeting', $translator->translate('greeting', ['name2' => 'John Doe'], 'general'));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de-DE');

        $this->assertEquals('Hello :name', $translator->translate('greeting', ['name2' => 'John Doe'], 'general'));
        $this->assertTranslationCacheNotEmpty($translator);

        $this->assertEquals('Hello {{name}}', $translator->translate('greeting2', ['name2' => 'John Doe'], 'general'));
        $this->assertTranslationCacheNotEmpty($translator);
    }

    public function testTranslateWithTranslateableKeyAndWithNoPlaceholer(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('greeting', $translator->translateWithoutPlaceholders('greeting', 'general'));
        $this->assertTranslationCacheNotEmpty($translator);
    }

    public function testTranslateWithNonTranslateableKeyNoPlaceHolder(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('C62', $translator->translateWithoutPlaceholders('C62', 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de-DE');

        $this->assertEquals('C62', $translator->translateWithoutPlaceholders('C62', 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);
    }

    public function testForceReloadOnAddDirectoryNotRegistered(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('unitcodes.C62', $translator->translate('unitcodes.C62', []));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->addLanguageDirectory(__DIR__);
        $this->assertTranslationCacheEmpty($translator);
    }

    public function testForceReloadOnAddDirectoryAlreadyRegistered(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('unitcodes.C62', $translator->translate('unitcodes.C62', []));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->addLanguageDirectory(__DIR__ . '/../assets');
        $this->assertTranslationCacheNotEmpty($translator);
    }

    public function testLanguagePriority(): void
    {
        $translator = new ZugferdVisualizerDefaultTranslator();
        $translator->clearLanguageDirectories();
        $translator->addLanguageDirectory(__DIR__ . '/../assets');

        $this->assertEquals('Invoice', $translator->translate('380', [], 'documenttype'));
        $this->assertEquals('Piece', $translator->translate('H87', [], 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de-DE');

        $this->assertEquals('Rechnungsbeleg', $translator->translate('380', [], 'documenttype'));
        $this->assertEquals('St.', $translator->translate('H87', [], 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de-AT');

        $this->assertEquals('Rchng', $translator->translate('380', [], 'documenttype'));
        $this->assertEquals('S.', $translator->translate('H87', [], 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de-DE');

        $this->assertEquals('Rechnungsbeleg', $translator->translate('380', [], 'documenttype'));
        $this->assertEquals('St.', $translator->translate('H87', [], 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('de');

        $this->assertEquals('Rechnung', $translator->translate('380', [], 'documenttype'));
        $this->assertEquals('St.', $translator->translate('H87', [], 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);

        $translator->setCurrentLanguage('en');

        $this->assertEquals('Invoice', $translator->translate('380', [], 'documenttype'));
        $this->assertEquals('Piece', $translator->translate('H87', [], 'unitcodes'));
        $this->assertTranslationCacheNotEmpty($translator);
    }

    private function assertTranslationCacheEmpty($translator): void
    {
        $cache = $this->getPrivatePropertyFromObject($translator, 'translationsCache')->getValue($translator);

        $this->assertEmpty($cache);
    }

    private function assertTranslationCacheNotEmpty(ZugferdVisualizerDefaultTranslator $translator): void
    {
        $cache = $this->getPrivatePropertyFromObject($translator, 'translationsCache')->getValue($translator);

        $this->assertNotEmpty($cache);
    }
}
