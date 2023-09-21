<?php

namespace horstoeko\zugferdvisualizer\tests\testcases;

use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\exception\ZugferdVisualizerNoTemplateDefinedException;
use horstoeko\zugferdvisualizer\exception\ZugferdVisualizerNoTemplateNotExistsException;
use horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerDefaultRenderer;
use horstoeko\zugferdvisualizer\tests\TestCase;
use horstoeko\zugferdvisualizer\ZugferdVisualizer;

class VisualizerText extends TestCase
{
    /**
     * @var ZugferdDocumentReader
     */
    protected static $document;

    public static function setUpBeforeClass(): void
    {
        self::$document = ZugferdDocumentReader::readAndGuessFromFile(dirname(__FILE__) . "/../assets/invoice_1.xml");
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::__construct
     */
    public function testConstructionNoRenderer(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);

        $this->assertNotNull($this->getPrivatePropertyFromObject($visualizer, 'document')->getValue($visualizer));
        $this->assertNull($this->getPrivatePropertyFromObject($visualizer, 'renderer')->getValue($visualizer));
        $this->assertEmpty($this->getPrivatePropertyFromObject($visualizer, 'template')->getValue($visualizer));
        $this->assertEmpty($this->getPrivatePropertyFromObject($visualizer, 'pdfFontDirectories')->getValue($visualizer));
        $this->assertEmpty($this->getPrivatePropertyFromObject($visualizer, 'pdfFontData')->getValue($visualizer));
        $this->assertEquals("dejavusans", $this->getPrivatePropertyFromObject($visualizer, 'pdfFontDefault')->getValue($visualizer));
        $this->assertEquals("A4-P", $this->getPrivatePropertyFromObject($visualizer, 'pdfPaperSize')->getValue($visualizer));
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::__construct
     */
    public function testConstructionGivenRenderer(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document, new ZugferdVisualizerDefaultRenderer());

        $this->assertNotNull($this->getPrivatePropertyFromObject($visualizer, 'document')->getValue($visualizer));
        $this->assertNotNull($this->getPrivatePropertyFromObject($visualizer, 'renderer')->getValue($visualizer));
        $this->assertInstanceOf(ZugferdVisualizerDefaultRenderer::class, $this->getPrivatePropertyFromObject($visualizer, 'renderer')->getValue($visualizer));
        $this->assertEmpty($this->getPrivatePropertyFromObject($visualizer, 'template')->getValue($visualizer));
        $this->assertEmpty($this->getPrivatePropertyFromObject($visualizer, 'pdfFontDirectories')->getValue($visualizer));
        $this->assertEmpty($this->getPrivatePropertyFromObject($visualizer, 'pdfFontData')->getValue($visualizer));
        $this->assertEquals("dejavusans", $this->getPrivatePropertyFromObject($visualizer, 'pdfFontDefault')->getValue($visualizer));
        $this->assertEquals("A4-P", $this->getPrivatePropertyFromObject($visualizer, 'pdfPaperSize')->getValue($visualizer));
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::setRenderer
     */
    public function testSetRenderer(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);

        $this->assertNull($this->getPrivatePropertyFromObject($visualizer, 'renderer')->getValue($visualizer));

        $visualizer->setRenderer(new ZugferdVisualizerDefaultRenderer());

        $this->assertNotNull($this->getPrivatePropertyFromObject($visualizer, 'renderer')->getValue($visualizer));
        $this->assertInstanceOf(ZugferdVisualizerDefaultRenderer::class, $this->getPrivatePropertyFromObject($visualizer, 'renderer')->getValue($visualizer));
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::setTemplate
     */
    public function testSetTemplate(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);

        $this->assertEmpty($this->getPrivatePropertyFromObject($visualizer, 'template')->getValue($visualizer));

        $visualizer->setTemplate('test');

        $this->assertEquals("test", $this->getPrivatePropertyFromObject($visualizer, 'template')->getValue($visualizer));
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::setDefaultTemplate
     */
    public function testSetDefaultTemplate(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);

        $this->assertNull($this->getPrivatePropertyFromObject($visualizer, 'renderer')->getValue($visualizer));
        $this->assertEmpty($this->getPrivatePropertyFromObject($visualizer, 'template')->getValue($visualizer));

        $visualizer->setDefaultTemplate();

        $this->assertNotNull($this->getPrivatePropertyFromObject($visualizer, 'renderer')->getValue($visualizer));
        $this->assertInstanceOf(ZugferdVisualizerDefaultRenderer::class, $this->getPrivatePropertyFromObject($visualizer, 'renderer')->getValue($visualizer));
        $this->assertStringContainsString('default.tmpl', $this->getPrivatePropertyFromObject($visualizer, 'template')->getValue($visualizer));
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::addPdfFontDirectory
     */
    public function testAddPdfFontDirectory(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);

        $this->assertEmpty($this->getPrivatePropertyFromObject($visualizer, 'pdfFontDirectories')->getValue($visualizer));

        $visualizer->addPdfFontDirectory('/invalidpath');

        $this->assertEmpty($this->getPrivatePropertyFromObject($visualizer, 'pdfFontDirectories')->getValue($visualizer));

        $visualizer->addPdfFontDirectory('/home');

        $this->assertNotEmpty($this->getPrivatePropertyFromObject($visualizer, 'pdfFontDirectories')->getValue($visualizer));
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::setPdfFontDefault
     */
    public function testSetPdfFontDefault(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);

        $this->assertEquals("dejavusans", $this->getPrivatePropertyFromObject($visualizer, 'pdfFontDefault')->getValue($visualizer));

        $visualizer->setPdfFontDefault('courier');

        $this->assertNotEquals("dejavusans", $this->getPrivatePropertyFromObject($visualizer, 'pdfFontDefault')->getValue($visualizer));
        $this->assertEquals("courier", $this->getPrivatePropertyFromObject($visualizer, 'pdfFontDefault')->getValue($visualizer));
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::setPdfPaperSize
     */
    public function testSetPdfPaperSize(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);

        $this->assertEquals("A4-P", $this->getPrivatePropertyFromObject($visualizer, 'pdfPaperSize')->getValue($visualizer));

        $visualizer->setPdfPaperSize('INVALID1');

        $this->assertEquals("A4-P", $this->getPrivatePropertyFromObject($visualizer, 'pdfPaperSize')->getValue($visualizer));

        $visualizer->setPdfPaperSize('A4');

        $this->assertEquals("A4-P", $this->getPrivatePropertyFromObject($visualizer, 'pdfPaperSize')->getValue($visualizer));

        $visualizer->setPdfPaperSize('A4-L');

        $this->assertEquals("A4-L", $this->getPrivatePropertyFromObject($visualizer, 'pdfPaperSize')->getValue($visualizer));
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderMarkup
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testMustUseDefaultRenderer
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateIsSet
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateExists
     */
    public function testRenderMarkup(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);

        $this->expectException(ZugferdVisualizerNoTemplateDefinedException::class);

        $visualizer->renderMarkup();
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderMarkup
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testMustUseDefaultRenderer
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateIsSet
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateExists
     */
    public function testRenderMarkupTemplateNotSet(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);
        $visualizer->setRenderer(new ZugferdVisualizerDefaultRenderer());

        $this->expectException(ZugferdVisualizerNoTemplateDefinedException::class);

        $visualizer->renderMarkup();
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderMarkup
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testMustUseDefaultRenderer
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateIsSet
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateExists
     * @covers \horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerDefaultRenderer::templateExists
     */
    public function testRenderMarkupTemplateNotExists(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);
        $visualizer->setRenderer(new ZugferdVisualizerDefaultRenderer());
        $visualizer->setTemplate('/invalidpath/invalidfile.tmpl');

        $this->expectException(ZugferdVisualizerNoTemplateNotExistsException::class);

        $visualizer->renderMarkup();
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderMarkup
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testMustUseDefaultRenderer
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateIsSet
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateExists
     * @covers \horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerDefaultRenderer::render
     */
    public function testRenderMarkupWithDefaultTemplate(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);
        $visualizer->setDefaultTemplate();

        $markup = $visualizer->renderMarkup();

        $this->assertStringContainsString("<html>", $markup);
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderMarkup
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderPdf
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testMustUseDefaultRenderer
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateIsSet
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateExists
     */
    public function testRenderPdf(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);

        $this->expectException(ZugferdVisualizerNoTemplateDefinedException::class);

        $visualizer->renderPdf();
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderMarkup
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderPdf
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testMustUseDefaultRenderer
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateIsSet
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateExists
     */
    public function testRenderPdfTemplateNotSet(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);
        $visualizer->setRenderer(new ZugferdVisualizerDefaultRenderer());

        $this->expectException(ZugferdVisualizerNoTemplateDefinedException::class);

        $visualizer->renderPdf();
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderMarkup
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderPdf
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testMustUseDefaultRenderer
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateIsSet
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateExists
     * @covers \horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerDefaultRenderer::templateExists
     */
    public function testRenderPdfTemplateNotExists(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);
        $visualizer->setRenderer(new ZugferdVisualizerDefaultRenderer());
        $visualizer->setTemplate('/invalidpath/invalidfile.tmpl');

        $this->expectException(ZugferdVisualizerNoTemplateNotExistsException::class);

        $visualizer->renderPdf();
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderMarkup
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderPdf
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testMustUseDefaultRenderer
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateIsSet
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateExists
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::instanciatePdfEngine
     * @covers \horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerDefaultRenderer::render
     */
    public function testRenderPdfWithDefaultTemplate(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);
        $visualizer->setDefaultTemplate();

        $pdf = $visualizer->renderPdf();

        $this->assertNotEmpty($pdf);
        $this->assertStringStartsWith('%PDF-1.4', $pdf);
    }

    /**
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderMarkup
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderPdf
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::renderPdfFile
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testMustUseDefaultRenderer
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateIsSet
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::testTemplateExists
     * @covers \horstoeko\zugferdvisualizer\ZugferdVisualizer::instanciatePdfEngine
     * @covers \horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerDefaultRenderer::render
     */
    public function testRenderPdfFileWithDefaultTemplate(): void
    {
        $visualizer = new ZugferdVisualizer(static::$document);
        $visualizer->setDefaultTemplate();

        $toFilename = dirname(__FILE__) . "/invoice.pdf";

        $this->registerFileForTestMethodTeardown($toFilename);

        $visualizer->renderPdfFile($toFilename);

        $this->assertTrue(file_exists($toFilename));
    }
}
