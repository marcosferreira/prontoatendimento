<?php

/**
 * Helper functions para o dashboard
 */

if (!function_exists('getClassificacaoRiscoClass')) {
    /**
     * Retorna a classe CSS para a classificação de risco
     */
    function getClassificacaoRiscoClass($classificacao): string
    {
        switch (strtolower($classificacao)) {
            case 'vermelho':
                return 'high';
            case 'amarelo':
                return 'medium';
            case 'verde':
                return 'low';
            case 'azul':
                return 'minimal';
            default:
                return 'normal';
        }
    }
}

if (!function_exists('getClassificacaoRiscoCor')) {
    /**
     * Retorna a cor Bootstrap para a classificação de risco
     */
    function getClassificacaoRiscoCor($classificacao): string
    {
        switch (strtolower($classificacao)) {
            case 'vermelho':
                return 'danger';
            case 'amarelo':
                return 'warning';
            case 'verde':
                return 'success';
            case 'azul':
                return 'info';
            default:
                return 'secondary';
        }
    }
}

if (!function_exists('getStatusClass')) {
    /**
     * Retorna a classe CSS para o status do atendimento
     */
    function getStatusClass($status): string
    {
        switch (strtolower($status)) {
            case 'em andamento':
                return 'attention';
            case 'finalizado':
                return 'normal';
            case 'cancelado':
                return 'danger';
            case 'aguardando':
                return 'warning';
            case 'suspenso':
                return 'neutral';
            default:
                return 'normal';
        }
    }
}

if (!function_exists('getStatusCor')) {
    /**
     * Retorna a cor Bootstrap para o status do atendimento
     */
    function getStatusCor($status): string
    {
        switch (strtolower($status)) {
            case 'em andamento':
                return 'primary';
            case 'finalizado':
                return 'success';
            case 'cancelado':
                return 'danger';
            case 'aguardando':
                return 'warning';
            case 'suspenso':
                return 'secondary';
            default:
                return 'secondary';
        }
    }
}

if (!function_exists('getSeveridadeIcone')) {
    /**
     * Retorna o ícone Bootstrap para a severidade da notificação
     */
    function getSeveridadeIcone($severidade): string
    {
        switch (strtolower($severidade)) {
            case 'critica':
                return 'exclamation-triangle-fill';
            case 'alta':
                return 'exclamation-circle-fill';
            case 'media':
                return 'info-circle-fill';
            case 'baixa':
                return 'check-circle-fill';
            default:
                return 'info-circle';
        }
    }
}

if (!function_exists('getSeveridadeCorBootstrap')) {
    /**
     * Retorna a cor Bootstrap para a severidade da notificação
     */
    function getSeveridadeCorBootstrap($severidade): string
    {
        switch (strtolower($severidade)) {
            case 'critica':
                return 'danger';
            case 'alta':
                return 'warning';
            case 'media':
                return 'info';
            case 'baixa':
                return 'success';
            default:
                return 'secondary';
        }
    }
}

if (!function_exists('formatarTempo')) {
    /**
     * Formata tempo relativo (ex: "há 2 horas")
     */
    function formatarTempo($datetime): string
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) {
            return 'agora mesmo';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return "há {$minutes} minuto" . ($minutes > 1 ? 's' : '');
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return "há {$hours} hora" . ($hours > 1 ? 's' : '');
        } else {
            $days = floor($time / 86400);
            return "há {$days} dia" . ($days > 1 ? 's' : '');
        }
    }
}

if (!function_exists('formatarNumero')) {
    /**
     * Formata números grandes (ex: 1.2K, 2.5M)
     */
    function formatarNumero($numero): string
    {
        if ($numero >= 1000000) {
            return number_format($numero / 1000000, 1) . 'M';
        } elseif ($numero >= 1000) {
            return number_format($numero / 1000, 1) . 'K';
        }
        return number_format($numero);
    }
}

if (!function_exists('getPercentualVariacao')) {
    /**
     * Calcula percentual de variação entre dois valores
     */
    function getPercentualVariacao($valorAtual, $valorAnterior): array
    {
        if ($valorAnterior == 0) {
            return [
                'percentual' => $valorAtual > 0 ? 100 : 0,
                'tipo' => $valorAtual > 0 ? 'aumento' : 'neutro',
                'icone' => $valorAtual > 0 ? 'arrow-up' : 'dash'
            ];
        }
        
        $percentual = (($valorAtual - $valorAnterior) / $valorAnterior) * 100;
        
        return [
            'percentual' => abs(round($percentual, 1)),
            'tipo' => $percentual > 0 ? 'aumento' : ($percentual < 0 ? 'diminuicao' : 'neutro'),
            'icone' => $percentual > 0 ? 'arrow-up' : ($percentual < 0 ? 'arrow-down' : 'dash')
        ];
    }
}
