## Frobou System Permission ##

Sistema de verificação de permissões de uso.  
**Ainda em construção**

**Como funciona:**
As permissões são verificadas onde são necessárias, como mostra o exemplo:

    $config = new FrobouDbConfig(json_decode(file_get_contents(__DIR__ . './../database.json')));
    $connection = new FrobouDbConnection($config);
    $perms = new FrobouSystemPermission($connection);
    $user = $this->perms->login('test', 'pass', true);
    $exp = $user->getPermission('admin');
    $exp->can_select = false;
    $exp->can_insert = false;
    $exp->can_update = false;
    $exp->can_delete = false;
A ideia é ter permissões com níveis hierárquicos, admin.teste diz que o usuário tem permissão X no recurso teste da tela admin

Usamos o pacote [frobou-db-connect](https://github.com/frobou/frobou-db-connect) para a conexão com banco de dados.
Ao instanciar FrobouSystemPermission, todos os recursos necessários ficam disponíveis.

 - login($username, $password, $pass_in_plain = false)
 - createUser(SystemUser $user)
 - updateUser(SystemUser $user, array $where)
 - createGroup($name)
 - createResource($name, $permission)

e a instancia de SystemUser recebida no métido login fornece:

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
**Todo:**

 - Vinculação de permissões e grupos
 - Vinculação de permissões e usuários

