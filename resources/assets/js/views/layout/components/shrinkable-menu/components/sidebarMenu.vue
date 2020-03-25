<template>
  <!-- active-name: 激活菜单的name -->
  <!-- open-names: 展开的 Submenu 的 name 集合 -->
  <Menu :active-name="$route.name" :open-names="openNames" @on-select="changeMenu" accordion ref="sideMenu" theme="light"
        width="auto">
    <!-- 属性不为隐藏且存在孩子节点 -->
    <template v-for="item in menuList" v-if="!item.hidden && item.children">
      <!-- menu-item 没有子页面 -->
      <MenuItem :key="'menuitem' + item.name" :name="item.children[0].name"
                v-if="item.children.length === 1 && !item.showParent">
        <Icon :key="'menuicon' + item.name" :size="iconSize"
              :type="item.children[0].meta.icon" v-if="item.children[0].meta && item.children[0].meta.icon"></Icon>
        <span :key="'title' + item.name" v-if="item.children[0].meta && item.children[0].meta.title">{{ item.children[0].meta.title }}</span>
      </MenuItem>
      <!-- submenu 有子页面 -->
      <Submenu :key="item.name" :name="item.name" v-else>
        <template slot="title">
          <Icon :size="iconSize" :type="item.meta.icon" v-if="item.meta && item.meta.icon"></Icon>
          <span v-if="item.meta && item.meta.title">{{ item.meta.title }}</span>
        </template>
        <template v-for="child in item.children">
          <MenuItem
            :key="'menuitem' + child.name"
            :name="child.name">
            <Icon :key="'icon' + child.name" :size="iconSize" :type="child.meta.icon"
                  v-if="child.meta && child.meta.icon"></Icon>
            <span :key="'title' + child.name" v-if="child.meta && child.meta.title">{{ child.meta.title }}</span>
          </MenuItem>
        </template>
      </Submenu>
    </template>
  </Menu>
</template>

<script>
  export default {
    name: 'sidebarMenu',
    props: {
      menuList: Array,
      iconSize: Number,
      openNames: {
        type: Array
      }
    },
    methods: {
      // log(e){
      //   console.log(e)
      // },
      changeMenu(active) {
        this.$emit('on-change', active);
        // console.log(this.menuList)
      },
      itemTitle(item) {
        if (typeof item.title === 'object') {
          return this.$t(item.title.i18n);
        } else {
          return item.title;
        }
      }
    },
    updated() {
      this.$nextTick(() => {
        if (this.$refs.sideMenu) {
          this.$refs.sideMenu.updateOpened();
        }
      });
    },
    created() {

    }
  };
</script>

<style lang="scss" rel="stylesheet/scss" scoped>
  .layout-text {
    height: 25px;
    display: inline-block;
    white-space: nowrap;
    position: absolute;
  }
</style>
