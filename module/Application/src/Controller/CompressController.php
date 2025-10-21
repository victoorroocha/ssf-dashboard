<?php
namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Application\Service\OracleService;
use Application\Repository\CompressRepository;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Acl;

class CompressController extends BaseController
{
    private $pgAdapter;
    private $oracleService;
    private $compressRepository;

    public function __construct(
        Adapter $pgAdapter, 
        OracleService $oracleService = null, 
        CompressRepository $compressRepository = null,
        Acl $acl
    )
    {
        parent::__construct($acl); 
        $this->pgAdapter = $pgAdapter;
        $this->oracleService = $oracleService;
        $this->compressRepository = $compressRepository;
    }

    public function indexAction()
    {
        return new ViewModel([
            'title' => 'Compressor de PDF',
            'levels' => ['low', 'medium', 'high', 'extreme', 'super'],
        ]);
    }

    private function compressPdf($inputPath, $outputPath, $level = 'medium')
    {
         // Verifica se Ghostscript está disponível
        $gsCheck = shell_exec('which gs || whereis gs');
        if (empty($gsCheck)) {
            throw new \RuntimeException("Ghostscript não está instalado no servidor.");
        }

        // Verifica versão do Ghostscript
        $versionCheck = shell_exec('gs --version');
        error_log("DEBUG: Ghostscript version: " . trim($versionCheck ?? 'Não detectado'));
        
        // Configurações super otimizadas
        $settings = [
            'low' => [
                'resolution' => 150,
                'jpegQuality' => 80,
                'pdfsettings' => '/printer',
            ],
            'medium' => [
                'resolution' => 100, 
                'jpegQuality' => 60,
                'pdfsettings' => '/ebook',
            ],
            'high' => [
                'resolution' => 72,
                'jpegQuality' => 40,
                'pdfsettings' => '/screen',
            ],
            'extreme' => [
                'resolution' => 50,
                'jpegQuality' => 25,
                'pdfsettings' => '/screen',
            ],
            'super' => [
                'resolution' => 36,  // MUITO baixa
                'jpegQuality' => 15, // Qualidade mínima
                'pdfsettings' => '/screen',
            ]
        ];

        $s = $settings[$level] ?? $settings['medium'];

        // Comando Ghostscript ULTRA agressivo para níveis altos
        if (in_array($level, ['extreme', 'super'])) {
            $cmd = sprintf(
                'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 ' .
                '-dPDFSETTINGS=%s ' .
                '-dNOPAUSE -dQUIET -dBATCH ' .
                '-dAutoRotatePages=/None ' .
                '-dColorConversionStrategy=/sRGB ' .
                '-dProcessColorModel=/DeviceRGB ' .
                '-dConvertCMYKImagesToRGB=true ' .
                '-dColorImageDownsampleType=/Average ' .
                '-dColorImageResolution=%d ' .
                '-dGrayImageDownsampleType=/Average ' .
                '-dGrayImageResolution=%d ' .
                '-dMonoImageDownsampleType=/Subsample ' .
                '-dMonoImageResolution=%d ' .
                '-dDownsampleColorImages=true ' .
                '-dDownsampleGrayImages=true ' .
                '-dDownsampleMonoImages=true ' .
                '-dColorImageFilter=/DCTEncode ' .
                '-dGrayImageFilter=/DCTEncode ' .
                '-dJPEGQ=%d ' .
                '-dCompressFonts=true ' .
                '-dEmbedAllFonts=false ' .
                '-dSubsetFonts=true ' .
                '-dCompressPages=true ' .
                '-dDetectDuplicateImages=true ' .
                '-dDoThumbnails=false ' .
                '-dCreateJobTicket=false ' .
                '-dPreserveEPSInfo=false ' .
                '-dPreserveOPIComments=false ' .
                '-dPreserveOverprintSettings=false ' .
                '-dUCRandBGInfo=/Remove ' .
                '-dOptimize=true ' .
                '-dASCII85EncodePages=false ' .
                '-dAutoFilterColorImages=true ' .
                '-dAutoFilterGrayImages=true ' .
                '-dHaveTransparency=false ' .
                '-dFastWebView=true ' . // Otimiza para web
                '-sOutputFile=%s %s',
                $s['pdfsettings'],
                $s['resolution'], 
                $s['resolution'], 
                $s['resolution'],
                $s['jpegQuality'],
                escapeshellarg($outputPath),
                escapeshellarg($inputPath)
            );
        } else {
            // Comando normal para níveis baixos
            $cmd = sprintf(
                'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 ' .
                '-dPDFSETTINGS=%s ' .
                '-dNOPAUSE -dQUIET -dBATCH ' .
                '-dColorImageResolution=%d ' .
                '-dGrayImageResolution=%d ' .
                '-dMonoImageResolution=%d ' .
                '-dColorConversionStrategy=/sRGB ' .
                '-dJPEGQ=%d ' .
                '-dCompressFonts=true ' .
                '-dSubsetFonts=true ' .
                '-dOptimize=true ' .
                '-sOutputFile=%s %s',
                $s['pdfsettings'],
                $s['resolution'], 
                $s['resolution'], 
                $s['resolution'],
                $s['jpegQuality'],
                escapeshellarg($outputPath),
                escapeshellarg($inputPath)
            );
        }

        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0) {
            error_log("Ghostscript error: " . implode("\n", $output));
            throw new \RuntimeException("Erro ao comprimir PDF. Código: " . $returnVar);
        }

        $originalSize = filesize($inputPath);
        $compressedSize = filesize($outputPath);
        
        // Se ainda não está pequeno o suficiente, tenta método alternativo
        $targetRatio = [
            'super' => 0.1,     // 10% do original (160KB para 16KB)
            'extreme' => 0.2,   // 20% do original  
            'high' => 0.4,      // 40% do original
        ];
        
        if (isset($targetRatio[$level]) && $compressedSize > $originalSize * $targetRatio[$level]) {
            return $this->superCompress($inputPath, $outputPath, $level);
        }

        return [
            'original_size' => $originalSize,
            'compressed_size' => $compressedSize,
            'reduction' => round((1 - $compressedSize/$originalSize) * 100, 1) . '%'
        ];
    }
    // Método de compressão SUPER agressivo
    private function superCompress($inputPath, $outputPath, $level)
    {
        $resolution = $level === 'super' ? 24 : 36;
        $quality = $level === 'super' ? 10 : 20;
        
        $cmd = sprintf(
            'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 ' .
            '-dPDFSETTINGS=/screen ' .
            '-dNOPAUSE -dQUIET -dBATCH ' .
            '-dColorImageResolution=%d ' .
            '-dGrayImageResolution=%d ' .
            '-dMonoImageResolution=%d ' .
            '-dColorConversionStrategy=/Gray ' .  // Converte para escala de cinza!
            '-dProcessColorModel=/DeviceGray ' .
            '-dConvertCMYKImagesToRGB=true ' .
            '-dConvertRGBImagesToGray=true ' .
            '-dJPEGQ=%d ' .
            '-dCompressFonts=true ' .
            '-dEmbedAllFonts=false ' .
            '-dSubsetFonts=true ' .
            '-dCompressPages=true ' .
            '-dDoThumbnails=false ' .
            '-dDetectDuplicateImages=true ' .
            '-dOptimize=true ' .
            '-dFastWebView=true ' .
            '-dHaveTransparency=false ' .
            '-dCannotEmbedFontPolicy=/Warning ' .
            '-sOutputFile=%s %s',
            $resolution, $resolution, $resolution,
            $quality,
            escapeshellarg($outputPath),
            escapeshellarg($inputPath)
        );

        exec($cmd, $output, $returnVar);
        
        if ($returnVar !== 0) {
            throw new \RuntimeException("Erro na super compressão.");
        }

        return [
            'original_size' => filesize($inputPath),
            'compressed_size' => filesize($outputPath),
            'reduction' => round((1 - filesize($outputPath)/filesize($inputPath)) * 100, 1) . '%',
            'method' => 'super_compress'
        ];
    }



    public function uploadAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return new JsonModel(['error' => 'Método inválido. Use POST.']);
        }

        $file = $this->params()->fromFiles('file');
        
        // Debug detalhado do upload
        if (!$file) {
            error_log("DEBUG: Nenhum arquivo recebido no upload");
            error_log("DEBUG: Files received: " . print_r($_FILES, true));
            return new JsonModel(['error' => 'Nenhum arquivo foi enviado.']);
        }

        // Verifica erros de upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'Arquivo muito grande. Limite do servidor excedido.',
                UPLOAD_ERR_FORM_SIZE => 'Arquivo muito grande. Limite do formulário excedido.',
                UPLOAD_ERR_PARTIAL => 'Upload parcial do arquivo.',
                UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado.',
                UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária não encontrada.',
                UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever arquivo no disco.',
                UPLOAD_ERR_EXTENSION => 'Uma extensão PHP interrompeu o upload.'
            ];
            
            $errorMsg = $uploadErrors[$file['error']] ?? "Erro desconhecido no upload (Código: {$file['error']})";
            error_log("DEBUG: Upload error - {$file['error']}: {$errorMsg}");
            
            return new JsonModel(['error' => $errorMsg]);
        }

        // Verifica se é PDF
        if ($file['type'] !== 'application/pdf') {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if ($mimeType !== 'application/pdf') {
                error_log("DEBUG: Tipo de arquivo inválido. Tipo: {$mimeType}, Esperado: application/pdf");
                return new JsonModel(['error' => 'Por favor, envie apenas arquivos PDF.']);
            }
        }

        // Verifica tamanho do arquivo
        if ($file['size'] === 0) {
            return new JsonModel(['error' => 'Arquivo vazio ou corrompido.']);
        }

        $level = $this->params()->fromPost('level', 'medium');
        
        // Cria caminhos temporários
        $inputPath = sys_get_temp_dir() . '/' . uniqid('pdf_input_') . '.pdf';
        $outputPath = sys_get_temp_dir() . '/' . uniqid('pdf_output_') . '.pdf';

        // Move o arquivo uploadado
        if (!move_uploaded_file($file['tmp_name'], $inputPath)) {
            error_log("DEBUG: Falha ao mover arquivo uploadado para: {$inputPath}");
            return new JsonModel(['error' => 'Erro ao processar arquivo.']);
        }

        // Verifica se o arquivo foi movido corretamente
        if (!file_exists($inputPath) || filesize($inputPath) === 0) {
            error_log("DEBUG: Arquivo de input não existe ou está vazio: {$inputPath}");
            return new JsonModel(['error' => 'Arquivo corrompido após upload.']);
        }

        error_log("DEBUG: Arquivo recebido - Nome: {$file['name']}, Tamanho: {$file['size']}, Tipo: {$file['type']}");
        error_log("DEBUG: Input path: {$inputPath}");
        error_log("DEBUG: Output path: {$outputPath}");
        error_log("DEBUG: Level: {$level}");

        try {
            $stats = $this->compressPdf($inputPath, $outputPath, $level);
            
            error_log("DEBUG: Compressão bem-sucedida - Original: {$stats['original_size']}, Comprimido: {$stats['compressed_size']}");

            return new JsonModel([
                'success' => true,
                'stats' => $stats,
                'downloadUrl' => '/compress/download?file=' . basename($outputPath)
            ]);
            
        } catch (\Exception $e) {
            error_log("DEBUG: Erro na compressão: " . $e->getMessage());
            
            // Limpa arquivos temporários em caso de erro
            if (file_exists($inputPath)) unlink($inputPath);
            if (file_exists($outputPath)) unlink($outputPath);
            
            return new JsonModel(['error' => 'Erro ao comprimir PDF: ' . $e->getMessage()]);
        }
    }
    public function downloadAction()
    {
        $file = $this->params()->fromQuery('file');
        $path = sys_get_temp_dir() . '/' . basename($file);

        if (!file_exists($path)) {
            return $this->getResponse()->setStatusCode(404);
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        readfile($path);
        exit;
    }
}
