<h1 align="center">icp-沣西投资云平台</h1>

#### 安装扩展

```bash
# 克隆项目
git clone https://github.com/leslieeilsel/Laravue.git

# 切换到icp分支

# 安装composer依赖包
cd laravue
composer install

# 推荐使用yarn安装依赖包
yarn install (或 npm install)
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
# 将database/data/icp.sql还原到数据库
```

#### 编译运行

```bash
# 开发环境编译
npm run watch (或 npm run dev)
# 生产环境编译，压缩JS文件
npm run prod
```
