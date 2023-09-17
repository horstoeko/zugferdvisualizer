<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer;

use Dompdf\Dompdf;
use Dompdf\Options;
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
     * @var string
     */
    protected $pdfFontDirectory = "";

    /**
     * The PDF default font
     *
     * @var string
     */
    protected $pdfFontDefault = "dejavusans";

    /**
     * The PDF paper size
     *
     * @var string
     * @see \Dompdf\Adapter\CPDF::PAPER_SIZES for valid sizes
     */
    protected $pdfPaperSize = "a4";

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
     * @param  string $fontDirectory
     * @return void
     */
    public function setPdfFontDirectory(string $fontDirectory): void
    {
        if (!file_exists($fontDirectory)) {
            return;
        }

        $this->pdfFontDirectory = $fontDirectory;
    }

    /**
     * Sets the PDF default font
     *
     * @param  string $pdfFontDefault
     * @return void
     */
    public function setPdfFontDefault(string $pdfFontDefault): void
    {
        $this->pdfFontDefault = $pdfFontDefault;
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
        $this->testRendererIsSet();
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
        $pdf->loadHtml($markup);

        return $pdf->output();
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
        $pdf->loadHtml($markup);
        $pdf->render();

        $pdfContent = $pdf->output();

        file_put_contents($toFilename, $pdfContent);
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
     * Check if a renderer is defined. If no one is set then an exception
     * is raised
     *
     * @return void
     * @throws ZugferdVisualizerNoRendererDefinedException
     */
    private function testRendererIsSet(): void
    {
        if (!$this->renderer) {
            throw new ZugferdVisualizerNoRendererDefinedException();
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
     * @return Dompdf
     */
    private function instanciatePdfEngine(): Dompdf
    {
        $pdfOptions = new Options();
        $pdfOptions->setTempDir(sys_get_temp_dir() . '/dompdf');
        $pdfOptions->setDefaultFont($this->pdfFontDefault);
        $pdfOptions->setDefaultPaperSize($this->pdfPaperSize);

        $pdf = new Dompdf($pdfOptions);

        return $pdf;
    }
}
