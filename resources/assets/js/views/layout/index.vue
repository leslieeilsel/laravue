<style lang="scss" rel="stylesheet/scss">
  @import 'main.scss';
</style>
<template>
  <div class="main">

    <!-- sidebar start -->
    <div class="sidebar-menu-con">
      <div class="main-title">
        <!-- <img src="../../images/xixian.png" style="vertical-align:top;" width="30px"/> -->
        <span
          style="color: #fdfdfd;font-size: 20px;font-weight: 600;letter-spacing: 1px;margin-left: 5px;">{{title}}</span>
      </div>
      <shrinkable-menu
        :menu-list="menuList"
        :open-names="openedSubmenuArr">
      </shrinkable-menu>
    </div>
    <!-- sidebar end -->

    <!-- header start -->
    <div class="main-header-con">
      <div class="main-header">
        <div class="header-middle-con">
        </div>
        <div class="header-avator-con">
          <!-- 提醒事项 -->
          <!--          <Badge dot style="top: 20px;left: 110px;color: rgba(0, 0, 0, 0.65)">-->
          <!--            <a href="/#/sys-manage/notify">-->
          <!--              <Icon type="ios-notifications" size="20" color="rgba(0, 0, 0, 0.65)"></Icon>-->
          <!--            </a>-->
          <!--          </Badge>-->
          <!--dropdown start -->
          <div class="user-dropdown-menu-con">
            <Row align="middle" class="user-dropdown-innercon" justify="end" type="flex">
              <Dropdown @on-click="clickDropdown" transfer trigger="click">
                <a href="javascript:void(0)">
                  <span class="main-user-name" key="main-user-name" style="color: #000">{{ userName }}</span>
                  <Icon type="arrow-down-b"></Icon>
                </a>
                <DropdownMenu slot="list">
                  <DropdownItem name="userCenter">个人中心</DropdownItem>
                  <DropdownItem divided name="logout">退出登录</DropdownItem>
                </DropdownMenu>
              </Dropdown>
              <Avatar src="/images/avatar.png" style="margin-left: 10px;"></Avatar>
            </Row>
          </div>
          <!-- dropdown end -->
        </div>
      </div>
      <div class="tags-con">
        <tags-page-opened></tags-page-opened>
      </div>
    </div>
    <!-- header end -->

    <!-- router-view start -->
    <div :style="{left: '250px'}" class="single-page-con">
      <div class="single-page">
        <keep-alive :include="cachePage">
          <router-view></router-view>
        </keep-alive>
      </div>
    </div>
    <!-- router-view end -->
    <!-- layout footer -->
  </div>
</template>
<script>
  import shrinkableMenu from './components/shrinkable-menu/shrinkable-menu.vue';
  import tagsPageOpened from './components/tags-page-opened.vue';
  import breadcrumbNav from './components/breadcrumb-nav.vue';
  import messageTip from './components/message-tip.vue';
  import util from '../../libs/util.js';
  import {getRouter} from '../../api/system';
  import layout from '../../views/layout';
  import {logout} from "../../api/login";

  export default {
    components: {
      shrinkableMenu,
      tagsPageOpened,
      breadcrumbNav,
      messageTip
    },
    data() {
      return {
        menus: [],
        userName: '',
        openedSubmenuArr: this.$store.state.app.openedSubmenuArr,
        title: this.$store.state.settings.title
      };
    },
    computed: {
      menuList() {
        return this.menus;
      },
      pageTagsList() {
        // 打开的页面的页面对象
        return this.$store.state.app.pageOpenedList;
      },
      currentPath() {
        // 当前面包屑数组
        return this.$store.state.app.currentPath;
      },
      cachePage() {
        return this.$store.state.app.cachePage;
      },
      lang() {
        return this.$store.state.app.lang;
      },
      mesCount() {
        return this.$store.state.app.messageCount;
      }
    },
    methods: {
      init() {
        if (localStorage.getrouter) {
          this.menus = JSON.parse(localStorage.getrouter)
        } else {
          getRouter().then(data => {
            this.menus = this.filterAsyncRouter(data.result);
          });
        }
        this.userName = this.$store.getters.user.name;

        let messageCount = 3;
        this.messageCount = messageCount.toString();
      },
      getObjArr(name) {
        // localStorage 获取数组对象的方法
        return JSON.parse(window.localStorage.getItem(name));
      },
      saveObjArr(name, data) {
        // localStorage 存储数组对象的方法
        localStorage.setItem(name, JSON.stringify(data))
      },
      toggleClick() {
        this.shrink = !this.shrink;
      },
      clickDropdown(name) {
        if (name === 'userCenter') {
          // 用户中心
          // util.openNewPage(this, 'home');
          this.$router.push({
            name: 'profile'
          });
        } else if (name === 'logout') {
          // 退出登录
          logout().then(res => {
            // 清除已打开标签
            sessionStorage.setItem('tags', null);
            if (res.result === true) {
              location.reload();
            }
          });
        }
      },
      filterAsyncRouter(asyncRouterMap) {
        // 遍历后台传来的路由字符串，转换为组件对象
        return asyncRouterMap.filter(route => {
          if (route.component) {
            if (route.component === 'layout') {
              // Layout组件特殊处理
              route.component = layout;
            } else {
              route.component = require('@/' + route.component + '.vue');
            }
          }
          if (route.children && route.children.length) {
            route.children = this.filterAsyncRouter(route.children)
          }
          return true;
        });
      },
      checkTag(name) {
        let openpageHasTag = this.pageTagsList.some(item => {
          if (item.name === name) {
            return true;
          }
        });
        if (!openpageHasTag) {
          // 解决关闭当前标签后再点击回退按钮会退到当前页时没有标签的问题
          util.openNewPage(this, name, this.$route.params || {}, this.$route.query || {});
        }
      }
    },
    mounted() {
      let tags = JSON.parse(sessionStorage.getItem("tags"));
      if (tags) {
        this.$store.state.app.tags = tags;
      }
      this.init();
    }
  };
</script>
<style scoped>
  .sidebar-menu-con {
    width: 250px;
    overflow: scroll;
    white-space: nowrap;

  }

  .sidebar-menu-con::-webkit-scrollbar {
    width: 0 !important;
  }

  .sidebar-menu-con::-webkit-scrollbar {
    width: 0 !important;
    height: 0;
  }
</style>
