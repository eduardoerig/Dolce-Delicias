<?php
declare(strict_types=1);
namespace App\Models;
final class Promotion {
 public static function vigente(array $p, \DateTimeInterface $date): bool {
  if (!($p['ativa']??$p['ativo']??false)) return false;
  $day=$date->format('Y-m-d');
  if ((!empty($p['data_inicio']) && $day<$p['data_inicio']) || (!empty($p['data_fim']) && $day>$p['data_fim'])) return false;
  return match($p['tipo_agenda']) {
   'SEMANAL'=>in_array((int)$date->format('w'),$p['dias_semana'],true),
   'MENSAL'=>in_array((int)$date->format('n'),$p['meses'],true),
   'PERIODO','SEMPRE'=>true,
   default=>false
  };
 }
 public static function visivel(array $p, \DateTimeInterface $date): bool {
  if (!($p['ativa']??false)) return false;
  if ((!empty($p['data_inicio']) && $date->format('Y-m-d')<$p['data_inicio']) || (!empty($p['data_fim']) && $date->format('Y-m-d')>$p['data_fim'])) return false;
  return $p['tipo_agenda']==='SEMANAL' || self::vigente($p,$date);
 }
}
