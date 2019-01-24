<template>
  <div>
    <div class="icon-search">
      <input
        type="text"
        v-model="key"
        :placeholder="tip"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
      >
    </div>
    <div class="icon-block icon-bar">
      <div class="icon-wrap" v-for="(item, i) in icon" :key="i" @click="handleChoose(item)">
        <div class="icons-item">
          <Icon :type="item" style="font-size: 32px;"/>
          <p>{{item}}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import {icons} from "../../libs/icon.js";
  import './icon.css';

  export default {
    name: "iconChoose",
    data() {
      return {
        icon: [],
        key: "",
        tip: "输入英文关键词搜索，比如 success"
      };
    },
    methods: {
      handleInput() {
        if (this.key) {
          // 搜索
          let re = [];
          icons.forEach(e => {
            e.tags.forEach(item => {
              if (item.indexOf(this.key) >= 0) {
                e.icons.forEach(r => {
                  re.push(r);
                });
              }
            });
          });
          this.icon = re;
        }
      },
      handleFocus() {
        if (!this.key) {
          this.tip = "";
        }
      },
      handleBlur() {
        if (!this.key) {
          this.tip = "输入英文关键词搜索，比如 success";
        }
      },
      handleChoose(v) {
        this.$emit("on-select", v);
      }
    },
    created() {
      let re = [];
      icons.forEach(e => {
        e.icons.forEach(item => {
          re.push(item);
        });
      });
      this.icon = re;
    }
  };
</script>

