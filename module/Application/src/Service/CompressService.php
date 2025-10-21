<?php
namespace Application\Service;

class CompressService
{
    public function compressPdf(string $inputPath, string $outputPath, string $level = 'medium'): array
    {
        $levels = [
            'high' => '/printer', // 300 DPI
            'medium' => '/ebook', // 150 DPI
            'low' => '/screen',   // 96 DPI
        ];

        $pdfSetting = $levels[$level] ?? '/ebook';
        $input = escapeshellarg($inputPath);
        $outputTmp = escapeshellarg($outputPath . '.tmp.pdf');
        $output = escapeshellarg($outputPath);

        // 🧩 Etapa 1 — Ghostscript
        $cmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 "
             . "-dPDFSETTINGS={$pdfSetting} -dNOPAUSE -dQUIET -dBATCH "
             . "-dAutoRotatePages=/None -sOutputFile={$outputTmp} {$input}";
        exec($cmd . " 2>&1", $log, $ret1);
        if ($ret1 !== 0) {
            throw new \RuntimeException('Erro ao executar Ghostscript: ' . implode("\n", $log));
        }

        // 🧩 Etapa 2 — QPDF (linearização e otimização final)
        $cmd2 = "qpdf --linearize {$outputTmp} {$output}";
        exec($cmd2 . " 2>&1", $log2, $ret2);
        if ($ret2 !== 0) {
            throw new \RuntimeException('Erro ao executar QPDF: ' . implode("\n", $log2));
        }

        // 🧩 Etapa 3 — Limpeza e estatísticas
        @unlink($outputPath . '.tmp.pdf');
        $orig = filesize($inputPath);
        $comp = filesize($outputPath);

        return [
            'original' => $orig,
            'compressed' => $comp,
            'reduction' => round(100 - ($comp / $orig * 100), 2)
        ];
    }
}
