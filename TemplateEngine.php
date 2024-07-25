<?php


class TemplateEngine
{
  private array $globalTemplateData = [];
  protected $patterns = [];

 

  public function __construct(private string $basePath, $templatePatterns)
  {
    $this->patterns = include $templatePatterns;
  }

  public function render(string $template, array $data = [])
  {
    extract($data, EXTR_SKIP);
    extract($this->globalTemplateData, EXTR_SKIP);

    ob_start();

    include $this->resolve($template);

    $output = ob_get_contents();

    ob_end_clean();

    return $output;
  }

  public function renderTemplate(string $template, array $data = []){

    extract($data, EXTR_SKIP);
    extract($this->globalTemplateData, EXTR_SKIP);

    $content = file_get_contents($this->resolve($template));

    $content = $this->searchAndReplace($content);
    eval( '?>' . $content );

  }

  public function resolve(string $path)
  {
    return "{$this->basePath}/{$path}";
  }

  public function addGlobal(string $key, mixed $value)
  {
    $this->globalTemplateData[$key] = $value;
  }

  public function searchAndReplace($source){
    foreach($this->patterns as $pattern ){
        $source = preg_replace($pattern['pattern'], $pattern['replace'], $source);
    }
    return $source;
}
}