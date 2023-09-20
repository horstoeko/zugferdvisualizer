<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer;

use Mpdf\Mpdf;
use Mpdf\Config\FontVariables;
use Mpdf\Config\ConfigVariables;
use horstoeko\zugferd\ZugferdDocument;
use horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerDefaultRenderer;
use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerMarkupRendererContract;
use horstoeko\zugferdvisualizer\exception\ZugferdVisualizerNoRendererDefinedException;
use horstoeko\zugferdvisualizer\exception\ZugferdVisualizerNoTemplateDefinedException;
use horstoeko\zugferdvisualizer\exception\ZugferdVisualizerNoTemplateNotExistsException;

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
     * @var \horstoeko\zugferd\ZugferdDocument
     */
    protected $document = null;

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
     * Constructor
     *
     * @param ZugferdDocument                              $document
     * @param ZugferdVisualizerMarkupRendererContract|null $renderer
     */
    public function __construct(ZugferdDocument $document, ?ZugferdVisualizerMarkupRendererContract $renderer = null)
    {
        $this->document = $document;

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
     * @param string $pdfPaperSize
     * @return void
     */
    public function setPdfPaperSize(string $pdfPaperSize): void
    {
        if (preg_match('/([0-9a-zA-Z]*)-([P,L])/i', $pdfPaperSize, $m)) {
            $this->pdfPaperSize = $pdfPaperSize;
        }
    }

    /**
     * Renders the markup (HTML)
     *
     * @return string
     * @throws ZugferdVisualizerNoRendererDefinedException
     * @throws ZugferdVisualizerNoTemplateDefinedException
     * @throws ZugferdVisualizerNoTemplateNotExistsException
     */
    public function renderMarkup(): string
    {
        $this->testMustUseDefaultRenderer();
        $this->testTemplateIsSet();
        $this->testTemplateExists();

        return $this->renderer->render($this->document, $this->template);
    }

    /**
     * Renders the PDF by markup (HTML) and returns the PDF as a string
     *
     * @return string
     */
    public function renderPdf(): string
    {
        $markup = $this->renderMarkup();

        $pdf = $this->instanciatePdfEngine();
        $pdf->WriteHTML($markup);

        return $pdf->Output('dummy.pdf', 'S');
    }

    /**
     * Renders the PDF by markup (HTML) to a physical file
     *
     * @param  string $toFilename
     * @return void
     */
    public function renderPdfFile(string $toFilename): void
    {
        $markup = $this->renderMarkup();

        $pdf = $this->instanciatePdfEngine();
        $pdf->WriteHTML($markup);
        $pdf->Output($toFilename, 'F');
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

        $pdf = new Mpdf(
            [
            'tempDir' => sys_get_temp_dir() . '/mpdf',
            'fontDir' => array_merge($defaultFontDirs, $this->pdfFontDirectories),
            'fontdata' => $defaultFontData + $this->pdfFontData,
            'default_font' => $this->pdfFontDefault,
            'format' => $this->pdfPaperSize,
            'PDFA' => true,
            'PDFAauto' => true,
            ]
        );

        return $pdf;
    }
}
