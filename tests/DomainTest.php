<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use App\Models\Promotion;
use App\Core\{Validator,ValidationException,Csrf,HttpException};
use App\Services\UploadService;
final class DomainTest extends TestCase {
 private function p(string $type): array {return ['ativa'=>true,'tipo_agenda'=>$type,'dias_semana'=>[3],'meses'=>[1,7],'data_inicio'=>null,'data_fim'=>null];}
 public function testWeeklyVisibilityIsNotValidity(): void {
  $p=$this->p('SEMANAL');$mon=new DateTimeImmutable('2026-09-14');$wed=new DateTimeImmutable('2026-09-16');
  self::assertTrue(Promotion::visivel($p,$mon));self::assertFalse(Promotion::vigente($p,$mon));self::assertTrue(Promotion::vigente($p,$wed));
 }
 public function testMonthlyAndAlways(): void {
  self::assertTrue(Promotion::vigente($this->p('MENSAL'),new DateTimeImmutable('2026-01-01')));
  self::assertFalse(Promotion::visivel($this->p('MENSAL'),new DateTimeImmutable('2026-02-01')));
  self::assertTrue(Promotion::vigente($this->p('SEMPRE'),new DateTimeImmutable('2026-02-01')));
 }
 public function testDateLimitsAndInactive(): void {
  $p=$this->p('PERIODO');$p['data_inicio']='2026-12-01';$p['data_fim']='2026-12-24';
  self::assertTrue(Promotion::vigente($p,new DateTimeImmutable('2026-12-24')));
  self::assertFalse(Promotion::visivel($p,new DateTimeImmutable('2026-12-25')));
  $p['ativa']=false;self::assertFalse(Promotion::vigente($p,new DateTimeImmutable('2026-12-10')));
 }
 public function testInvalidPromotion(): void {
  $this->expectException(ValidationException::class);
  Validator::validate('promocoes',['nome'=>'Teste','slug'=>'teste','tipo_desconto'=>'PERCENTUAL','valor_desconto'=>'101','tipo_atendimento'=>'AMBOS','tipo_agenda'=>'PERIODO','data_inicio'=>'2026-12-24','data_fim'=>'2026-12-01'],null);
 }
 public function testPasswordHash(): void {$hash=password_hash('UmaSenhaLonga123',PASSWORD_DEFAULT);self::assertTrue(password_verify('UmaSenhaLonga123',$hash));self::assertFalse(password_verify('errada',$hash));}
 public function testCsrf(): void {$_SESSION=[];$token=Csrf::token();Csrf::check($token);self::assertSame(64,strlen($token));$this->expectException(HttpException::class);Csrf::check('forjado');}
 public function testUploadValidationAndTraversal(): void {
  $u=new UploadService();self::assertNull($u->get('../../etc/passwd'));
  $file=tempnam(sys_get_temp_dir(),'dolce');
  try {file_put_contents($file,base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jfZkAAAAASUVORK5CYII='));self::assertSame('png',$u->validate($file,'image',filesize($file)));file_put_contents($file,'%PDF-1.4' . "\n%%EOF");self::assertSame('pdf',$u->validate($file,'pdf',filesize($file)));file_put_contents($file,'<?php echo 1;');$this->expectException(ValidationException::class);$u->validate($file,'image',filesize($file));} finally {unlink($file);}
 }
 public function testPricePackageConversion(): void {$f=dd_faixa(['valor'=>120,'por'=>'100 unidades','minPedido'=>50]);self::assertEquals(1.2,$f['unitario']);self::assertSame(50,$f['passo']);}
}
