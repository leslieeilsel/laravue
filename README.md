<h1 align="center">Laravue</h1>
<div align="center">
基于Laravel和Vue实现的后台管理系统
</div>
## 安装

#### 安装扩展

```bash
# 克隆项目
git clone https://github.com/leslieeilsel/Laravue.git

# 安装composer依赖
cd laravue
composer install

# 推荐使用yarn安装扩展包
yarn install
```
#### 配置

```bash
# 复制配置文件
cp .env.example .env

# 生成加密key
php artisan key:generate

# 生成jwt加密key
php artisan jwt:secret

# 配置env中的数据库链接
配置数据库名称、用户名和密码

# 数据库迁移
php artisan migrate
php artisan db:seed
```

#### 编译

```bash
# 开发环境编译
npm run dev (或 npm run watch 监控文件修改)
# 生产环境编译，压缩JS文件
npm run prod
```
