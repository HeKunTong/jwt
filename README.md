## JSON Web Token(JWT)

JWT是为了在网络应用环境间传递声明而执行的一种基于JSON的开放标准（(RFC 7519).该token被设计为紧凑且安全的，特别适用于分布式站点的单点登录（SSO）场景。JWT的声明一般被用来在身份提供者和服务提供者间传递被认证的用户身份信息，以便于从资源服务器获取资源，也可以增加一些额外的其它业务逻辑所必须的声明信息，该token也可直接被用于认证，也可被加密。

对于jwt陌生的开发者可以参考: <https://www.jianshu.com/p/576dbf44b2ae>

## 克隆项目

```
git clone https://github.com/HeKunTong/jwt.git
```

## 安装依赖

```
composer install
```

## 生成easyswoole配置文件和命令

```
php vender/easyswoole/easyswoole/bin/easyswoole.php install
```

## 启动项目

```
php easyswoole start
```
