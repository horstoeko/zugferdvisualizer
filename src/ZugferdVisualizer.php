<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerMarkupRendererContract;
use horstoeko\zugferdvisualizer\exception\ZugferdVisualizerNoTemplateDefinedException;
use horstoeko\zugferdvisualizer\exception\ZugferdVisualizerNoTemplateNotExistsException;
use horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerDefaultRenderer;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Exception\AssetFetchingException;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;

/**
 * Class representing the main visualizer class
 *
 * @category ZugferdVisualizer
 * @package  ZugferdVisualizer
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdvisualizer
 */
class ZugferdVisualizer
{
    /**
     * The zugferd document
     *
     * @var \horstoeko\zugferd\ZugferdDocumentReader
     */
    protected $documentReader = null;

    /**
     * The renderer to use
     *
     * @var \horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerMarkupRendererContract
     */
    protected $renderer = null;

    /**
     * The template for the renderer to use
     *
     * @var string
     */
    protected $template = "";

    /**
     * The directories where to search for additional fonts
     *
     * @var array
     * @see https://mpdf.github.io/fonts-languages/fonts-in-mpdf-7-x.html
     */
    protected $pdfFontDirectories = [];

    /**
     * The definitions for additional fonts
     *
     * @var array
     * @see https://mpdf.github.io/fonts-languages/fonts-in-mpdf-7-x.html
     */
    protected $pdfFontData = [];

    /**
     * The PDF default font
     *
     * @var string
     * @see https://mpdf.github.io/fonts-languages/default-font.html
     */
    protected $pdfFontDefault = "dejavusans";

    /**
     * The PDF paper size
     * Notation: <Format>-[P|L]
     *
     * @var string
     * @see https://mpdf.github.io/paging/page-size-orientation.html
     */
    protected $pdfPaperSize = "A4-P";

    /**
     * A callbacl which is called before MPDF is instanciated. Here
     * is the possibillity to set custom options for MPDF
     *
     * @var callable|null
     */
    protected $mpdfPreInitCallback = null;

    /**
     * A callbacl which is called after MPDF is instanciated. Here
     * is the possibillity to set custom options for MPDF
     *
     * @var callable|null
     */
    protected $mpdfRuntimeInitCallback = null;

    /**
     * Factory for creating a visualizer by a ZugferdDocumentReader
     *
     * @param ZugferdDocumentReader $documentReader
     * @param ZugferdVisualizerMarkupRendererContract|null $renderer
     * @return ZugferdVisualizer
     */
    public static function fromDocumentReader(ZugferdDocumentReader $documentReader, ?ZugferdVisualizerMarkupRendererContract $renderer = null): ZugferdVisualizer
    {
        return new static($documentReader, $renderer);
    }

    /**
     * Factory for creating a visualizer by a ZugferdDocumentReader
     *
     * @param ZugferdDocumentBuilder $documentBuilder
     * @param ZugferdVisualizerMarkupRendererContract|null $renderer
     * @return ZugferdVisualizer
     */
    public static function fromDocumentBuilder(ZugferdDocumentBuilder $documentBuilder, ?ZugferdVisualizerMarkupRendererContract $renderer = null)
    {
        $documentReader = ZugferdDocumentReader::readAndGuessFromContent($documentBuilder->getContent());

        return static::fromDocumentReader($documentReader, $renderer);
    }

    /**
     * Constructor
     *
     * @param  ZugferdDocumentReader                        $documentReader
     * @param  null|ZugferdVisualizerMarkupRendererContract $renderer
     * @return void
     * @deprecated v2.0.0 Direct call of constructor will be removed in the future. Use static factory methods instead
     */
    public function __construct(ZugferdDocumentReader $documentReader, ?ZugferdVisualizerMarkupRendererContract $renderer = null)
    {
        $this->documentReader = $documentReader;

        if ($renderer) {
            $this->setRenderer($renderer);
        }
    }

    /**
     * Setup the renderer to use for generating markup
     *
     * @param  ZugferdVisualizerMarkupRendererContract $renderer
     * @return void
     */
    public function setRenderer(ZugferdVisualizerMarkupRendererContract $renderer): void
    {
        $this->renderer = $renderer;
    }

    /**
     * Set the template to use in the specified renderer
     *
     * @param  string $template
     * @return void
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * Sets the built-in template (and switch the markup-rendering engine to the default renderer)
     *
     * @return void
     */
    public function setDefaultTemplate(): void
    {
        $this->setRenderer(new ZugferdVisualizerDefaultRenderer());
        $this->setTemplate(dirname(__FILE__) . "/template/default.tmpl");
    }

    /**
     * Add an additional directory where the PDF-Engine will
     * search for fonts
     *
     * @param  string $directory
     * @return void
     */
    public function addPdfFontDirectory(string $directory): void
    {
        if (!file_exists($directory)) {
            return;
        }

        $this->pdfFontDirectories[] = $directory;
    }

    /**
     * Add a font definition
     *
     * - Example 1: ``$visualizer->addPdfFont('frutiger', 'R', 'Frutiger-Normal.ttf')``
     * - Example 2: ``$visualizer->addPdfFont('frutiger', 'I', 'FrutigerObl-Normal.ttf')``
     *
     * @param string $name
     * @param string $style
     * @param string $filename
     * @return void
     */
    public function addPdfFontData(string $name, string $style, string $filename): void
    {
        $this->pdfFontData[$name][$style] = $filename;
    }

    /**
     * Sets the default PDF default font
     *
     * @param  string $pdfFontDefault
     * @return void
     */
    public function setPdfFontDefault(string $pdfFontDefault): void
    {
        $this->pdfFontDefault = $pdfFontDefault;
    }

    /**
     * Sets the PDF papersize
     *
     * @param  string $pdfPaperSize
     * @return void
     */
    public function setPdfPaperSize(string $pdfPaperSize): void
    {
        if (preg_match('/([0-9a-zA-Z]*)-([P,L])/i', $pdfPaperSize, $_)) {
            $this->pdfPaperSize = $pdfPaperSize;
        }
    }

    /**
     * Set the callback which is called before the internal instance
     * of the PDF-Engine is instanciated
     *
     * @param  callable $callback
     * @return void
     */
    public function setPdfPreInitCallback(callable $callback): void
    {
        $this->mpdfPreInitCallback = $callback;
    }

    /**
     * Set the callback which is called after the internal instance
     * of the PDF-Engine is instanciated
     *
     * @param  callable $callback
     * @return void
     */
    public function setPdfRuntimeInitCallback(callable $callback): void
    {
        $this->mpdfRuntimeInitCallback = $callback;
    }

    /**
     * Renders the markup (HTML)
     *
     * @return string
     * @throws ZugferdVisualizerNoTemplateDefinedException
     * @throws ZugferdVisualizerNoTemplateNotExistsException
     */
    public function renderMarkup(): string
    {
        $this->testMustUseDefaultRenderer();
        $this->testTemplateIsSet();
        $this->testTemplateExists();

        return $this->renderer->render($this->documentReader, $this->template);
    }

    /**
     * Renders the PDF by markup (HTML) and returns the PDF as a string
     *
     * @return string
     * @throws ZugferdVisualizerNoTemplateDefinedException
     * @throws ZugferdVisualizerNoTemplateNotExistsException
     * @throws MpdfException
     * @throws AssetFetchingException
     * @throws RuntimeException
     * @throws Exception
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws PdfTypeException
     * @throws InvalidArgumentException
     */
    public function renderPdf(): string
    {
        return $this->internalRenderPdf($this->renderMarkup(), "S", "dummy.pdf");
    }

    /**
     * Renders the PDF by markup (HTML) to a physical file
     *
     * @param  string $toFilename
     * @return void
     * @throws ZugferdVisualizerNoTemplateDefinedException
     * @throws ZugferdVisualizerNoTemplateNotExistsException
     * @throws MpdfException
     * @throws AssetFetchingException
     * @throws RuntimeException
     * @throws Exception
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws PdfTypeException
     * @throws InvalidArgumentException
     */
    public function renderPdfFile(string $toFilename): void
    {
        $this->internalRenderPdf($this->renderMarkup(), "F", $toFilename);
    }

    /**
     * @param  string $markup
     * @param  string $outputDestination
     * @param  string $toFilename
     * @return string|void
     * @throws MpdfException
     * @throws AssetFetchingException
     * @throws RuntimeException
     * @throws Exception
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws PdfTypeException
     * @throws InvalidArgumentException
     */
    protected function internalRenderPdf(string $markup, string $outputDestination, string $toFilename)
    {
        $pdf = $this->instanciatePdfEngine();
        $pdf->WriteHTML($markup);

        return $pdf->Output($toFilename, $outputDestination);
    }

    /**
     * If no renderer is specified the default renderer is
     * instanciated and used
     *
     * @return void
     */
    private function testMustUseDefaultRenderer(): void
    {
        if (!$this->renderer) {
            $this->setRenderer(new ZugferdVisualizerDefaultRenderer());
        }
    }

    /**
     * Check if a template for the renderer is defined. If no one is set then an exception
     * is raised
     *
     * @return void
     * @throws ZugferdVisualizerNoTemplateDefinedException
     */
    private function testTemplateIsSet(): void
    {
        if (!$this->template) {
            throw new ZugferdVisualizerNoTemplateDefinedException();
        }
    }

    /**
     * Checks if the given template exists. If no one is set then an exception
     * is raised
     *
     * @return void
     * @throws ZugferdVisualizerNoTemplateNotExistsException
     */
    private function testTemplateExists(): void
    {
        if (!$this->renderer->templateExists($this->template)) {
            throw new ZugferdVisualizerNoTemplateNotExistsException();
        }
    }

    /**
     * Returns a new instance of the PDF-Engine (MPDF)
     *
     * @return Mpdf
     */
    private function instanciatePdfEngine(): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $defaultFontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $defaultFontData = $defaultFontConfig['fontdata'];

        $config = [
            'tempDir' => sys_get_temp_dir() . '/mpdf',
            'fontDir' => array_merge($defaultFontDirs, $this->pdfFontDirectories),
            'fontdata' => $defaultFontData + $this->pdfFontData,
            'default_font' => $this->pdfFontDefault,
            'format' => $this->pdfPaperSize,
            'PDFA' => true,
            'PDFAauto' => true,
        ];

        if (is_callable($this->mpdfPreInitCallback)) {
            $config = call_user_func($this->mpdfPreInitCallback, $config, $this);
        }

        $pdf = new Mpdf($config);

        if (is_callable($this->mpdfRuntimeInitCallback)) {
            call_user_func($this->mpdfRuntimeInitCallback, $pdf, $this);
        }

        return $pdf;
    }
}
