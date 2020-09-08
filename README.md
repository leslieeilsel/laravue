<h1 align="center">laravue（重构中）</h1>
<div align="center">
基于 Laravel 和 Vue 实现的后台管理系统
</div>

![登录页](./storage/screenshot/login.png)


#### 安装扩展

```bash
# 克隆项目
git clone https://github.com/leslieeilsel/laravue.git

# 安装 composer 依赖
cd Laravue
composer install

# 推荐使用 yarn 安装扩展包
cnpm install
```
#### 配置

```bash
# 复制配置文件
cp .env.example .env

# 生成加密 key
php artisan key:generate

# 生成 jwt 加密 key
php artisan jwt:secret

# 配置 env 中的数据库链接
配置数据库名称、用户名和密码

# 数据库迁移
php artisan migrate
php artisan db:seed

# 将 database/data/laravue.sql 导入数据库
```

#### 编译运行

```bash
# 开发环境编译
npm run dev、npm run watch（监控文件修改）
# 生产环境编译，压缩 js 文件
npm run prod
```

#### 更多截图

https://github.com/leslieeilsel/laravue/tree/master/storage/screenshot


#### TODO
- [ ] 使用完全前后台分离模式
- [ ] 前台基于 ant-design-pro-vue 重构（正在进行）
- [x] 后台基于Laravel 7 重构
- [ ] GraphQL 实践
