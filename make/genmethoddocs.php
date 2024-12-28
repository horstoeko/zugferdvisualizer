<?php

use horstoeko\stringmanagement\StringUtils;
use horstoeko\zugferdvisualizer\contracts\ZugferdVisualizerMarkupRendererContract;
use horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerDefaultRenderer;
use horstoeko\zugferdvisualizer\renderer\ZugferdVisualizerLaravelRenderer;
use horstoeko\zugferdvisualizer\ZugferdVisualizer;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Printer;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Exception\PcreException;
use Webmozart\Assert\InvalidArgumentException;

require dirname(__FILE__) . "/../vendor/autoload.php";

class CustomPhpPrinter extends Printer
{
    public function __construct()
    {
        parent::__construct();

        $this->indentation = '  ';
    }
}

class ExtractClass
{
    /**
     * The class to analyze
     *
     * @var string
     */
    protected $className = "";

    /**
     * Class + method name to ignore in inheritance check
     *
     * @var array
     */
    protected $ignoreInheritance = [];

    /**
     * Constructor
     *
     * @param string $className
     * @param array  $ignoreInheritance
     */
    public function __construct(string $className, array $ignoreInheritance = [])
    {
        $this->className = $className;
        $this->ignoreInheritance = $ignoreInheritance;
    }

    /**
     * Returns the current classnane
     *
     * @return string
     * */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Returns the base name of the current classname
     *
     * @return string
     */
    public function getClassBasename(): string
    {
        $classParts = explode('\\', $this->className);
        return end($classParts);
    }

    /**
     * Magic method __toString, String converstion
     *
     * @return string
     * @throws InvalidArgumentException
     * @throws PcreException
     * @throws LogicException
     */
    public function __toString()
    {
        return $this->getJson();
    }

    /**
     * Returns the result as array
     *
     * @return array
     * @throws InvalidArgumentException
     * @throws PcreException
     * @throws LogicException
     */
    public function getArray(): array
    {
        $reflection = new ReflectionClass($this->className);
        $classDocComment = $reflection->getDocComment();
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC); // Only public methods
        $docBlockFactory = DocBlockFactory::createInstance();
        $result = [];
        $result['methods'] = [];

        if ($classDocComment !== false) {
            $classDocBlock = $docBlockFactory->create($classDocComment);
            $deprecatedTag = $classDocBlock->getTagsByName('deprecated');
            $result['class'] = [
                'summary' => $classDocBlock->getSummary() ?: '',
                'description' => (string)$classDocBlock->getDescription() ?: '',
                'deprecated' => !empty($deprecatedTag) ? (string)$deprecatedTag[0] : ''
            ];
        } else {
            $result['class'] = [
                'summary' => '',
                'description' => '',
                'deprecated' => ''
            ];
        }

        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->getName() != $this->className) {
                if (!in_array(sprintf('%s::%s', $this->className, $method->getName()), $this->ignoreInheritance)) {
                    continue;
                }
            }

            $docComment = $method->getDocComment();
            $parameters = [];
            $returnDetails = [
                'type' => 'void',
                'description' => ''
            ];
            $methodDetails = [
                'summary' => '',
                'description' => '',
                'static' => false,
                'abstract' => false,
                'final' => false,
                'hasadditional' => false,
                'deprecated' => '',
            ];

            if ($docComment !== false) {
                $docBlock = $docBlockFactory->create($docComment);

                // Extract summary and description
                $methodDetails['summary'] = $docBlock->getSummary() ?: 'No summary available.';
                $methodDetails['description'] = (string)$docBlock->getDescription() ?: '';
                $methodDetails['static'] = $method->isStatic();
                $methodDetails['abstract'] = $method->isAbstract();
                $methodDetails['final'] = $method->isFinal();
                $methodDetails['hasadditional'] = $method->isStatic() || $method->isAbstract() || $method->isFinal();
                $deprecatedTag = $docBlock->getTagsByName('deprecated');
                if (!empty($deprecatedTag)) {
                    $methodDetails['deprecated'] = (string)$deprecatedTag[0];
                }

                // Parse @param tags
                $paramDescriptions = [];
                foreach ($docBlock->getTagsByName('param') as $tag) {
                    if ($tag instanceof Param) {
                        $paramDescriptions[$tag->getVariableName()] = [
                            'type' => (string) $tag->getType(),
                            'description' => (string) $tag->getDescription()
                        ];
                    }
                }

                // Parse @return tag
                $returnTag = $docBlock->getTagsByName('return');
                if (!empty($returnTag) && $returnTag[0] instanceof Return_) {
                    $returnDetails['type'] = (string) $returnTag[0]->getType();
                    $returnDetails['description'] = (string) $returnTag[0]->getDescription();
                }
            }

            // Get method parameters and match them with DocBlock descriptions
            foreach ($method->getParameters() as $parameter) {
                $parameterName = $parameter->getName();
                $parameterType = $parameter->getType();
                $parameterTypeString = "";

                if ($parameterType instanceof ReflectionUnionType) {
                    $types = $parameterType->getTypes();
                    foreach ($types as $type) {
                        $parameterTypeString .= $type->getName() . '|';
                    }
                    $parameterTypeString = rtrim($parameterTypeString, '|');
                } elseif ($parameterType instanceof ReflectionNamedType) {
                    $parameterTypeString = $parameterType->getName();
                } else {
                    $parameterTypeString = 'mixed';
                }

                $parameters[] = [
                    'name' => $parameterName,
                    'type' => $parameterTypeString ? $parameterTypeString : 'mixed',
                    'isNullable' => $parameterType && $parameterType->allowsNull(),
                    'defaultValueavailable' => $parameter->isOptional() ? ($parameter->isDefaultValueAvailable() ? true : false) : false,
                    'defaultValue' => $parameter->isOptional() ? ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null) : null,
                    'description' => $paramDescriptions[$parameterName]['description'] ?? ''
                ];
            }

            $result['methods'][$method->getName()] = [
                'methodDetails' => $methodDetails,
                'parameters' => $parameters,
                'return' => $returnDetails
            ];
        }

        return $result;
    }

    /**
     * Returns the result as JSON string
     *
     * @return string
     * @throws InvalidArgumentException
     * @throws PcreException
     * @throws LogicException
     */
    public function getJson(): string
    {
        return json_encode($this->getArray(), JSON_PRETTY_PRINT);
    }

    /**
     * Save Json to file
     *
     * @param string $filename
     * @return void
     * @throws InvalidArgumentException
     */
    public function saveJson(string $filename): void
    {
        file_put_contents($filename, $this->getJson());
    }
}

class MarkDownGenerator
{
    /**
     * Extractor
     *
     * @var ExtractClass
     */
    protected $extractor = null;

    /**
     * The lines for the MD
     *
     * @var string[]
     */
    protected $lines = [];

    /**
     * Constructor
     *
     * @param ExtractClass $extractor
     */
    public function __construct(ExtractClass $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * Generate markdown
     *
     * @return MarkDownGenerator
     */
    public function generateMarkdown(): MarkDownGenerator
    {
        $metaData = $this->extractor->getArray();

        $this->addLineH2("Summary");

        $phpPrinter = new CustomPhpPrinter();
        $phpClass = new ClassType($this->extractor->getClassBasename());

        if (!empty($metaData['class']['summary'])) {
            $this->addLine($this->removeSprintfPlaceholder($metaData['class']['summary'] ?? ""))->addEmptyLine();
        }

        if (!empty($metaData['class']['description'])) {
            $this->addLine($this->removeSprintfPlaceholder($metaData['class']['description'] ?? ""))->addEmptyLine();
        }

        if (!empty($metaData['class']['deprecated'])) {
            $this->addLine("> [!CAUTION]");
            $this->addLine("> Deprecated %s", $metaData['class']['deprecated']);
            $this->addEmptyLine();
        }

        $this->addExample(dirname(__FILE__) . sprintf('/md/%s.md', $this->extractor->getClassBasename()), true);

        if (!empty($metaData['methods'])) {
            $this->addLineH2("Methods");
        }

        foreach ($metaData['methods'] as $methodName => $methodData) {
            $this->addLineH3($methodName, $methodData["methodDetails"]["hasadditional"] === false);

            if ($methodData["methodDetails"]["static"] === true) {
                $this->addToLastLine('<span style="color: white; background-color: blue; padding: 0.2em 0.5em; border-radius: 0.2em; font-size: .8rem">``[static]``</span>', " ");
            }

            if ($methodData["methodDetails"]["abstract"] === true) {
                $this->addToLastLine('<span style="color: white; background-color: red; padding: 0.2em 0.5em; border-radius: 0.2em; font-size: .8rem">``[abstract]``</span>', " ");
            }

            if ($methodData["methodDetails"]["final"] === true) {
                $this->addToLastLine('<span style="color: white; background-color: green; padding: 0.2em 0.5em; border-radius: 0.2em; font-size: .8rem">``[final]``</span>', " ");
            }

            if ($methodData["methodDetails"]["hasadditional"] === true) {
                $this->addEmptyLine();
            }

            if (!empty($methodData["methodDetails"]["deprecated"])) {
                $this->addLine("> [!CAUTION]");
                $this->addLine("> Deprecated %s", $methodData["methodDetails"]["deprecated"]);
                $this->addEmptyLine();
            }

            $this->addLineH4("Summary");

            if (!empty($methodData["methodDetails"]["summary"])) {
                $this->addLineItalic($this->removeSprintfPlaceholder($methodData["methodDetails"]["summary"]))->addEmptyLine();
            }

            if (!empty($methodData["methodDetails"]["description"])) {
                $this->addLineItalic($this->removeSprintfPlaceholder($methodData["methodDetails"]["description"]))->addEmptyLine();
            }

            $this->addLineH4("Signature");

            $phpMethod = $phpClass->addMethod($methodName);
            $phpMethod->setPublic();
            $phpMethod->setStatic($methodData["methodDetails"]["static"] === true);
            $phpMethod->setAbstract($methodData["methodDetails"]["abstract"] === true);
            $phpMethod->setFinal($methodData["methodDetails"]["final"] === true);
            $phpMethod->setReturnType($this->fixPhpType($methodData["return"]["type"]));
            //$phpMethod->setBody(null);

            foreach ($methodData["parameters"] as $parameter) {
                $phpParameter = $phpMethod
                    ->addParameter($parameter["name"])
                    ->setType($this->fixPhpType($parameter["type"]))
                    ->setNullable($parameter["isNullable"]);

                if ($parameter['defaultValueavailable'] === true) {
                    $phpParameter->setDefaultValue($parameter["defaultValue"]);
                }
            }

            $this->addLineRaw("```php");
            $this->addLineRaw($phpPrinter->printMethod($phpMethod));
            $this->addLineRaw("```");

            if (!empty($methodData["parameters"])) {
                $this->addLineH4("Parameters");
                $this->addLine("| Name | Type | Allows Null | Description");
                $this->addLine("| :------ | :------ | :-----: | :------");

                foreach ($methodData["parameters"] as $parameter) {
                    $this->addLine(
                        "| %s | %s | %s | %s",
                        $parameter["name"],
                        $parameter["type"],
                        $this->boolToMarkDown($parameter["isNullable"] ? "Yes" : "No"),
                        $parameter["description"] ?? "",
                    );
                }

                $this->addEmptyLine();
            } else {
                $this->addEmptyLine();
            }

            if ($methodData["return"]["type"] && $methodData["return"]["type"] != "void") {
                $this->addLineH4("Returns");
                $this->addLineRaw(sprintf("Returns a value of type __%s__", $methodData["return"]["type"]));
                $this->addEmptyLine();
            }

            $this->addExample(dirname(__FILE__) . sprintf('/md/%s_%s.md', $this->extractor->getClassBasename(), $methodName));
        }

        return $this;
    }

    /**
     * Save MD to file
     *
     * @param string $filename
     * @return MarkDownGenerator
     */
    public function saveToFile(string $filename): MarkDownGenerator
    {
        file_put_contents($filename, implode(PHP_EOL, $this->lines));

        return $this;
    }

    /**
     * Add a line to internal container
     *
     * @param string $string
     * @param mixed ...$args
     * @return MarkDownGenerator
     */
    private function addLine(string $string, ...$args): MarkDownGenerator
    {
        if (StringUtils::stringIsNullOrEmpty($string)) {
            return $this;
        }

        $this->lines[] = $this->sanatizeString(sprintf($string, ...$args));

        return $this;
    }

    /**
     * Add a line to internal container
     *
     * @param string $string
     * @param mixed ...$args
     * @return MarkDownGenerator
     */
    private function addLineRaw(string $string, ...$args): MarkDownGenerator
    {
        if (StringUtils::stringIsNullOrEmpty($string)) {
            return $this;
        }

        $this->lines[] = sprintf($string, ...$args);

        return $this;
    }

    /**
     * Add a line to internal container
     *
     * @param string $string
     * @param mixed ...$args
     * @return MarkDownGenerator
     */
    private function addLineRawAllowEmpty(string $string, ...$args): MarkDownGenerator
    {
        $this->lines[] = sprintf($string, ...$args);

        return $this;
    }

    /**
     * Add an empty line to internal container
     *
     * @return MarkDownGenerator
     */
    private function addEmptyLine(): MarkDownGenerator
    {
        $this->lines[] = "";

        return $this;
    }

    /**
     * Add an H1-Line to internal container
     *
     * @param string $string
     * @param boolean $newLine
     * @return MarkDownGenerator
     */
    private function addLineH1(string $string, bool $newLine = true): MarkDownGenerator
    {
        $this->addLine("# %s", $string);

        if ($newLine) {
            $this->addEmptyLine();
        }

        return $this;
    }

    /**
     * Add an H2-Line to internal container
     *
     * @param string $string
     * @param boolean $newLine
     * @return MarkDownGenerator
     */
    private function addLineH2(string $string, bool $newLine = true): MarkDownGenerator
    {
        $this->addLine("## %s", $string);

        if ($newLine) {
            $this->addEmptyLine();
        }

        return $this;
    }

    /**
     * Add an H3-Line to internal container
     *
     * @param string $string
     * @param boolean $newLine
     * @return MarkDownGenerator
     */
    private function addLineH3(string $string, bool $newLine = true): MarkDownGenerator
    {
        $this->addLine("### %s", $string);

        if ($newLine) {
            $this->addEmptyLine();
        }

        return $this;
    }

    /**
     * Add an H4-Line to internal container
     *
     * @param string $string
     * @param boolean $newLine
     * @return MarkDownGenerator
     */
    private function addLineH4(string $string, bool $newLine = true): MarkDownGenerator
    {
        $this->addLine("#### %s", $string);

        if ($newLine) {
            $this->addEmptyLine();
        }

        return $this;
    }

    /**
     * Add a string to the latest line which was added
     *
     * @param string $string
     * @param string $delimiter
     * @param mixed ...$args
     * @return MarkDownGenerator
     */
    private function addToLastLine(string $string, string $delimiter = "", ...$args): MarkDownGenerator
    {
        if (empty($this->lines)) {
            return $this->addLine($string, ...$args);
        }

        $lastIndex = count($this->lines) - 1;
        $this->lines[$lastIndex] = $this->lines[$lastIndex] . $delimiter . sprintf($string, ...$args);

        return $this;
    }

    /**
     * Add line as italic formatted
     *
     * @param string $string
     * @param mixed ...$args
     * @return MarkDownGenerator
     */
    private function addLineItalic(string $string, ...$args): MarkDownGenerator
    {
        return $this->addLine(sprintf("_%s_", $string), ...$args);
    }

    /**
     * Add line as bold formatted
     *
     * @param string $string
     * @param mixed ...$args
     * @return MarkDownGenerator
     */
    private function addLineBold(string $string, ...$args): MarkDownGenerator
    {
        return $this->addLine(sprintf("__%s__", $string), ...$args);
    }

    /**
     * Import an example from a markdown file
     *
     * @param string $exampleFilename
     * @param bool $isClass
     * @return MarkDownGenerator
     */
    private function addExample(string $exampleFilename, bool $isClass = false): MarkDownGenerator
    {
        if (!file_exists($exampleFilename)) {
            return $this;
        }

        $exampleFileContent = file_get_contents($exampleFilename);

        if ($exampleFileContent === false) {
            return $this;
        }

        if ($isClass === true) {
            $this->addLineH2("Example");
        } else {
            $this->addLineH4("Example");
        }

        $exampleFileContent = str_replace(array("\r\n", "\r", "\n"), "\n", $exampleFileContent);

        foreach (explode("\n", $exampleFileContent) as $exampleFileContentLine) {
            $this->lines[] = $exampleFileContentLine;
        }

        $this->addEmptyLine();

        return $this;
    }

    /**
     * Sanatize a string
     *
     * @param string $string
     * @return string
     */
    private function sanatizeString(string $string): string
    {
        $string = str_replace("\n", "<br/>", $string);
        $string = str_replace("__BT-, From __", "", $string);
        $string = str_replace("__BT-, From", "__BT-??, From", $string);
        $string = trim($string);

        return $string;
    }

    /**
     * Remove sprintf placeholders
     *
     * @param string $string
     * @return string
     */
    private function removeSprintfPlaceholder(string $string): string
    {
        $string = str_replace("%", "", $string);

        return $string;
    }

    /**
     * Fix the PHP type
     *
     * @param string $string
     * @return string
     */
    private function fixPhpType(string $string): string
    {
        if (stripos($string, '[]') !== false) {
            $string = 'array';
        }
        if (stripos($string, 'array<') === 0) {
            $string = 'array';
        }
        if ($string == '$this') {
            $string = 'static';
        }

        return $string;
    }

    /**
     * Convert yes/no to markdown markup
     *
     * @param string $boolText
     * @return string
     */
    private function boolToMarkDown(string $boolText): string
    {
        return strcasecmp($boolText, "no") === 0 ? ":x:" : ":heavy_check_mark:";
    }
}

class BatchMarkDownGenerator
{
    /**
     * Start a batch documentation creation
     *
     * @param array $classes
     * @return void
     * @throws InvalidArgumentException
     * @throws PcreException
     * @throws LogicException
     */
    public static function generate(array $classes, array $ignoreInheritance = [])
    {
        foreach ($classes as $className => $toFilename) {
            $extractor = new ExtractClass($className, $ignoreInheritance);
            $generator = new MarkDownGenerator($extractor);
            $generator->generateMarkdown();
            $generator->saveToFile($toFilename);
        }
    }
}

BatchMarkDownGenerator::generate([
    ZugferdVisualizer::class => dirname(__FILE__) . '/Class-ZugferdVisualizer.md',
    ZugferdVisualizerMarkupRendererContract::class => dirname(__FILE__) . '/Class-ZugferdVisualizerMarkupRendererContract.md',
    ZugferdVisualizerDefaultRenderer::class => dirname(__FILE__) . '/Class-ZugferdVisualizerDefaultRenderer.md',
    ZugferdVisualizerLaravelRenderer::class => dirname(__FILE__) . '/Class-ZugferdVisualizerLaravelRenderer.md',
], [
    // Nothing here
]);
