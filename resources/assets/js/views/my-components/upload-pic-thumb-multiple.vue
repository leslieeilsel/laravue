<template>
  <div>
    <div :key="index" class="upload-list" v-for="(item,index) in uploadList">
      <template>
        <img :src="item.file_path">
        <div class="upload-list-cover">
          <Icon @click="handleView(item.file_path)" type="ios-eye-outline"></Icon>
          <Icon @click="handleRemove(item)" type="ios-trash-outline"></Icon>
        </div>
      </template>
    </div>
    <Upload
      :action="uploadFileUrl"
      :before-upload="handleBeforeUpload"
      :data="params"
      :format="['jpg','jpeg','png']"
      :max-size="600"
      :on-error="handleError"
      :on-exceeded-size="handleMaxSize"
      :on-format-error="handleFormatError"
      :on-success="handleSuccess"
      :show-upload-list="false"
      accept=".jpg, .jpeg, .png"
      ref="upload"
      style="display: inline-block;"
      type="drag"
    >
      <div style="width: 158px;height:100px;line-height: 100px;">
        <Icon size="20" type="md-add"></Icon>
      </div>
    </Upload>

    <Modal :styles="{top: '30px'}" :width="600" title="图片预览" v-model="viewImage">
      <img :src="imgUrl" alt="无效的图片链接" style="width: 100%;margin: 0 auto;display: block;">
      <div slot="footer">
        <Button @click="viewImage=false">关闭</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
  import {uploadFile} from "../../api/upload";

  export default {
    value: String,
    name: "uploadPicThumbMultiple",
    props: {
      multiple: {
        type: Boolean,
        default: true
      },
      params: {
        type: Object
      }
    },
    data() {
      return {
        currentValue: this.value,
        uploadFileUrl: uploadFile,
        uploadList: [],
        viewImage: false,
        imgUrl: ""
      };
    },
    methods: {
      init() {
      },
      handleView(imgUrl) {
        console.log(imgUrl)
        this.imgUrl = imgUrl;
        this.viewImage = true;
      },
      handleRemove(file) {
        const uploadList = this.uploadList;
        this.uploadList.splice(uploadList.indexOf(file), 1);
        this.returnValue();
      },
      handleSuccess(res, file) {
        if (res.data.success) {
          file.url = res.data.url;
          this.currentValue = res.data;
          // 单张图片处理
          if (!this.multiple && this.uploadList.length > 2) {
            // 删除第一张
            this.uploadList.splice(0, 1);
          }
          this.uploadList.push(this.currentValue.detail);
          // 返回组件值
          this.returnValue();
          this.$Message.success(res.message);
        } else {
          this.$Message.error(res.message);
        }
      },
      handleError(error, file, fileList) {
        this.$Message.error(error.toString());
      },
      handleFormatError(file) {
        this.$Notice.warning({
          title: "不支持的文件格式",
          desc:
            "所选文件‘ " +
            file.name +
            " ’格式不正确, 请选择 .jpg .jpeg .png 图片格式文件"
        });
      },
      handleMaxSize(file) {
        this.$Notice.warning({
          title: "文件大小过大",
          desc: "所选文件大小过大, 不得超过 600KB."
        });
      },
      handleBeforeUpload() {
        return true;
      },
      returnValue() {
        if (!this.uploadList || this.uploadList.length < 1) {
          if (!this.multiple) {
            this.$emit("on-change", "");
          } else {
            this.$emit("on-change", []);
          }
          return;
        }
        if (!this.multiple) {
          // 单张
          let v = this.uploadList[0].url;
          this.$emit("on-change", v);
        } else {
          let v = [];
          this.uploadList.forEach(e => {
            v.push(e);
          });
          this.$emit("on-change", v);
        }
      },
      setData(v) {
        if (v === null) {
          return;
        }
        if (typeof v == "string") {
          let item = {
            url: v,
            status: "finished"
          };
          this.uploadList.push(item);
        } else if (typeof v == "object") {
          v.forEach(e => {
            this.uploadList.push(e);
          });
        }
        this.returnValue();
      },
      handleClearFiles() {
        this.uploadList = [];
      }
    },
    mounted() {
      this.init();
    }
  };
</script>

<style lang="less">
  .upload-list {
    display: inline-block;
    width: 158px;
    height: 100px;
    text-align: center;
    line-height: 100px;
    border: 1px solid transparent;
    border-radius: 4px;
    overflow: hidden;
    background: #fff;
    position: relative;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
    margin-right: 5px;
  }

  .upload-list img {
    width: 100%;
    height: 100%;
  }

  .upload-list-cover {
    display: none;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.6);
  }

  .upload-list:hover .upload-list-cover {
    display: block;
  }

  .upload-list-cover i {
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    margin: 0 2px;
  }
</style>

