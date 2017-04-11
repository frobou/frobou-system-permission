## Frobou System Permission ##

Sistema de verificação de permissões de uso.

**Como funciona:**
As permissões são verificadas onde são necessárias, como mostra o exemplo:

    $config = new FrobouDbConfig(json_decode(file_get_contents(__DIR__ . './../database.json')));
    $connection = new FrobouDbConnection($config);
    $perms = new FrobouSystemPermission($connection);
    $user = $this->perms->login('test', 'pass', true);
    $exp = $user->getPermission('admin');
    var_dump($exp);
    object(stdClass)#140 (4) {
      ["can_select"]=>
      bool(true)
      ["can_insert"]=>
      bool(false)
      ["can_update"]=>
      bool(true)
      ["can_delete"]=>
      bool(false)
    }
    if ($exp->can_select){
    	echo "I can";
    }
    
A ideia é ter permissões com níveis hierárquicos, admin.teste diz que o usuário tem permissão X no recurso teste da tela admin

Usamos o pacote [frobou-db-connect](https://github.com/frobou/frobou-db-connect) para a conexão com banco de dados.
Ao instanciar FrobouSystemPermission, todos os recursos necessários ficam disponíveis.

 - login($username, $password, $pass_in_plain = false)
 - getUserList()
 - createUser(SystemUser $user)
 - updateUser(SystemUser $user, array $where)
 - deleteUser($username)
 - undeleteUser($username)
 - createGroup($name)
 - createResource($name, $permission)
 - registerGroupResource($username, $resourcename)
 - unregisterGroupResource($username, $resourcename)
 - registerUserResource($username, $resourcename)
 - unregisterUserResource($username, $resourcename)

e a instância de SystemUser recebida no métido login fornece, além dos dados do usuário:

 - getPermission($resource, $separator = '.')
 - getInsertString()
 - getUpdateString(array $where)
 - getSqlParams()

**Tipos de permissões:**

 - Permissões por grupo.
 - Permissões por usuário.
 - Permissões unificadas: 
	 - ver MERGE_PERMISSIONS 

## Usando: ##

Algumas constantes podem ser usada como uma forma de configuração do sistema.

 - MERGE_PERMISSIONS - Boolean: true faz com que as permissões de usuário subscrevam as permissões de grupo de mesmo nome e mescla ambos, passando a fornecer a junção das permissões resultantes.
 - BASE_PERMISSION - Boolean: true significa que se existirem permissões base, o valor atribuído a elas são retornados, caso contrario a permissão é 0
 - PASSWORD_SALT - String: o valor padrão é "default", se for informado um valor, ele será usado para a geração de senha. **Obs: uma senha gerada com um salt não será validada se o valor de PASSWORD_SALT for alterado**
 - TRUE_DELETE - Boolean: caso seja true, o registro do usuário será efetivamente excluído, caso contrário, somente desativado. **Obs: não existe tratamento pra exclusão das relações entre tabelas, o que significa que, antes de excluir o usuário, todos os registros vinculados devem ser excluídos, criando uma exception do tipo FrobouDbSgdbErrorException**

Testando login

    public function testLoginOk()
    {
        $user = $this->perms->login('test', 'pass', true);
        $this->assertInstanceOf(SystemUser::class, $user);
    }
Testando permissões

    public function testPermissionForResourceAdminDotTeste()
    {
        $user = $this->perms->login('test', 'pass', true);
        $exp = new \stdClass();
        $exp->can_select = true;
        $exp->can_insert = true;
        $exp->can_update = true;
        $exp->can_delete = true;
        $this->assertEquals($user->getPermission('admin.teste'), $exp);
    }
Criando um grupo

    public function testInsertGroup()
    {
        $this->assertTrue($this->perms->createGroup('grp_' . rand(0, 15988)));
    }
Criando uma permissão

    public function testInsertResource()
    {
        $this->assertTrue($this->perms->createResource('admin.test', 0));
    }
Criando um usuário

    public function testInsertUser()
    {
        $user = new SystemUser();
        $user->setActive(1)->setCanEdit(1)->setCanLogin(1)->setCanUseApi(1)
            ->setCanUseWeb(1)->setCreateDate()->setEmail('capitao@caverna.com')->setName('Novo Usuario')
            ->setPassword('senhanha')->setSystemGroup(1)->setUsername('username_' . rand(0, 12345))->setUserType('T');
        $this->assertTrue($this->perms->createUser($user));
    }
Vinculando grupo X permissão

	public function testRegisterGroupResource()
	{
		$this->perms->createResource('admin.com', 3);
		$this->assertTrue($this->perms->registerGroupResource('user', 'admin.com'));
    }
Desvinculando grupo X permissão
    
	public function testUnRegisterGroupResource()
	{
		$this->perms->createResource('admin.com', 3);
		$this->assertTrue($this->perms->unregisterGroupResource('user', 'admin.com'));
    }
Vinculando user X permissão
    
	public function testRegisterUserResource()
    {
        $this->perms->createResource('admin.com', 7);
        $this->assertTrue($this->perms->registerUserResource('ispti', 'admin.com'));
    }
Desvinculando user X permissão
    
	public function testRegisterUserResource()
    {
        $this->perms->createResource('admin.com', 7);
        $this->assertTrue($this->perms->unregisterUserResource('ispti', 'admin.com'));
    }
Testando Remover usuário (desativar)

    public function testDeleteUser(){
        $username = 'username_' . rand(0, 12345);
        $user = new SystemUser();
        $user->setActive(1)->setCanEdit(1)->setCanLogin(1)->setCanUseApi(1)
            ->setCanUseWeb(1)->setCreateDate()->setEmail('capitao@caverna.com')->setName('Novo Usuario')
            ->setPassword('senhanha')->setSystemGroup(1)->setUsername($username)->setUserType('T');
        $this->perms->createUser($user);
        $this->perms->createResource('admin.com', 3);
        $this->perms->registerGroupResource($username, 'admin.com');
        $this->perms->registerUserResource($username, 'admin.com');
        $this->assertTrue($this->perms->deleteUser($username));
    }
Testando reativar usuário

    public function testUndeleteUser(){
        $username = 'fabio';
        $user = new SystemUser();
        $user->setActive(1)->setCanEdit(1)->setCanLogin(1)->setCanUseApi(1)
            ->setCanUseWeb(1)->setCreateDate()->setEmail('capitao@caverna.com')->setName('Novo Usuario')
            ->setPassword('senhanha')->setSystemGroup(1)->setUsername($username)->setUserType('T');
        $this->perms->createUser($user);
        $this->perms->deleteUser($username);
        $this->assertTrue($this->perms->undeleteUser($username));
    }
Testando remover usuário (realmente)

	public function testDeleteUserReal(){
        define('TRUE_DELETE', true);
        $username = 'username_' . rand(0, 12345);
        $user = new SystemUser();
        $user->setActive(1)->setCanEdit(1)->setCanLogin(1)->setCanUseApi(1)
            ->setCanUseWeb(1)->setCreateDate()->setEmail('capitao@caverna.com')->setName('Novo Usuario')
            ->setPassword('senhanha')->setSystemGroup(1)->setUsername($username)->setUserType('T');
        $this->perms->createUser($user);
        $this->assertTrue($this->perms->deleteUser($username));
    }
