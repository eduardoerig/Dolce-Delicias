<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use App\Core\{Database,ValidationException};
use App\Repositories\{Repository,CatalogRepository};
use App\Services\{CatalogService,UploadService,AuthService};
final class DatabaseTest extends TestCase {
 private Repository $repo;
 private CatalogService $service;
 private string $schema;
 private int $admin;
 private int $category;
 private int $unit;
 protected function setUp(): void {
  if(getenv('RUN_DB_TESTS')!=='1')$this->markTestSkipped('Use RUN_DB_TESTS=1 em banco de testes.');
  $this->repo=new Repository(Database::connect());$this->schema='test_'.bin2hex(random_bytes(5));
  $this->repo->db->exec('CREATE SCHEMA '.$this->schema.'; SET search_path TO '.$this->schema);
  foreach(glob(DD_BASE.'/database/migrations/*.up.sql') as $migracao)$this->repo->db->exec(file_get_contents($migracao));
  $this->admin=$this->repo->save('usuarios',['nome'=>'Admin','login'=>'admin','senha_hash'=>password_hash('TesteSeguro123!',PASSWORD_DEFAULT),'perfil'=>'ADMIN'],null);
  $this->category=$this->repo->save('categorias',['nome'=>'Salgados','slug'=>'salgados'],null);
  $this->unit=$this->repo->save('unidades',['nome'=>'Matriz','slug'=>'matriz','tipo_unidade'=>'MATRIZ'],null);
  $this->service=new CatalogService($this->repo,new UploadService(sys_get_temp_dir().'/dolce-tests'));
 }
 protected function tearDown(): void {
  if(isset($this->repo)) {if($this->repo->db->inTransaction())$this->repo->db->rollBack();$this->repo->db->exec('SET search_path TO public; DROP SCHEMA '.$this->schema.' CASCADE');}
 }
 private function product(): array {return ['nome'=>'Pão','slug'=>'pao','id_categoria'=>(string)$this->category,'preco'=>'120.00','rotulo_preco'=>'100 unidades','pedido_minimo'=>'50','passo_quantidade'=>'50','ativo'=>'1','unidades'=>[(string)$this->unit,(string)$this->unit],'sabores'=>"Queijo\nQueijo",'tags'=>'sem carne, sem carne'];}
 public function testProductCreateUpdateStatusAndUniqueLinks(): void {
  $p=$this->product();$id=$this->service->save('produtos',$p,null,$this->admin);
  self::assertSame(1,(int)$this->repo->query('SELECT count(*) FROM produto_unidade WHERE id_produto=?',[$id])->fetchColumn());
  $p['preco']='130.00';$this->service->save('produtos',$p,$id,$this->admin);
  self::assertSame('130.00',$this->repo->find('produtos',$id)['preco']);
  self::assertCount(1,(new CatalogRepository($this->repo))->products());
  $this->service->status('produtos',$id,$this->admin);self::assertCount(0,(new CatalogRepository($this->repo))->products());
 }
 public function testDatabaseRejectsSecondMatrix(): void {
  $this->expectException(PDOException::class);$this->repo->save('unidades',['nome'=>'Outra','slug'=>'outra','tipo_unidade'=>'MATRIZ'],null);
 }
 public function testDatabaseRejectsInactiveMatrix(): void {
  $this->expectException(PDOException::class);$this->repo->save('unidades',['ativa'=>false],$this->unit);
 }
 public function testDatabaseRejectsDuplicateProductUnit(): void {
  $id=$this->service->save('produtos',$this->product(),null,$this->admin);
  $this->expectException(PDOException::class);$this->repo->query('INSERT INTO produto_unidade(id_produto,id_unidade) VALUES (?,?)',[$id,$this->unit]);
 }
 public function testBadRelationshipRollsBackProduct(): void {
  $p=$this->product();$p['unidades']=['999999'];
  try{$this->service->save('produtos',$p,null,$this->admin);self::fail('Esperava FK inválida.');}catch(ValidationException){}
  self::assertSame(0,(int)$this->repo->query('SELECT count(*) FROM produtos')->fetchColumn());
 }
 public function testAuthenticationAndRateLimit(): void {
  $auth=new AuthService($this->repo);self::assertNotNull($auth->verify('admin','TesteSeguro123!','test-ip'));self::assertNull($auth->verify('admin','incorreta','test-ip'));self::assertNull($auth->verify('desconhecido','incorreta','test-ip'));
  for($i=0;$i<7;$i++)$auth->verify('admin','incorreta','test-ip');
  $this->expectException(\App\Core\HttpException::class);$auth->verify('outro','incorreta','test-ip');
 }
 public function testPromotionIntersectsAvailability(): void {
  $product=$this->service->save('produtos',$this->product(),null,$this->admin);
  $p=['nome'=>'Quarta','slug'=>'quarta','tipo_desconto'=>'PERCENTUAL','valor_desconto'=>'20','tipo_agenda'=>'SEMANAL','dias_semana'=>'3','ativa'=>'1','produtos'=>[(string)$product],'unidades'=>[(string)$this->unit]];
  $this->service->save('promocoes',$p,null,$this->admin);$cat=new CatalogRepository($this->repo);self::assertCount(1,$cat->promotions());
  $this->repo->query('UPDATE produto_unidade SET disponivel=FALSE WHERE id_produto=?',[$product]);self::assertCount(0,$cat->promotions());
 }
 public function testDatabaseRejectsPromotionOver100AndInvalidDates(): void {
  $sql="INSERT INTO promocoes(nome,slug,tipo_desconto,valor_desconto,tipo_atendimento,tipo_agenda,data_inicio,data_fim) VALUES ('Oferta',?,'PERCENTUAL',?,'AMBOS','PERIODO',?,?)";
  foreach([['over','101','2026-01-01','2026-02-01'],['dates','20','2026-02-01','2026-01-01']] as $params){try{$this->repo->query($sql,$params);self::fail('Esperava CHECK.');}catch(PDOException $e){self::assertSame('23514',$e->getCode());}}
 }
}
