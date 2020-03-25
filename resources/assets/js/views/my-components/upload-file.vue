<template>
  <Upload
    :action="uploadFileUrl"
    :before-upload="beforeUpload"
    :data="params"
    :format="['jpg','jpeg','png','gif','bmp','docx','doc','xls','xlsx','pdf']"
    :max-size="10240"
    :on-error="handleError"
    :on-exceeded-size="handleMaxSize"
    :on-format-error="handleFormatError"
    :on-success="handleSuccess"
    :show-upload-list="false"
    accept=".jpg, .jpeg, .png, .gif, .bmp, .docx, .doc, .xls, .xlsx, .pdf"
    class="upload"
    ref="up"
  >
    <Button :icon="icon" :loading="loading" :size="size" type="info">{{buttonName}}</Button>
  </Upload>
</template>

<script>
  import {uploadFile} from "../../api/upload";

  export default {
    value: String,
    name: "uploadFile",
    props: {
      params: {
        type: Object
      },
      size: String,
      icon: {
        type: String,
        default: "ios-cloud-upload-outline"
      },
      buttonName: String,
    },
    data() {
      return {
        loading: false,
        viewImage: false,
        currentValue: this.value,
        uploadFileUrl: uploadFile
      };
    },
    methods: {
      init() {
      },
      handleFormatError(file) {
        this.loading = false;
        this.$Notice.warning({
          title: "不支持的文件格式",
          desc:
            "所选文件‘ " +
            file.name +
            " ’格式不正确, 请选择 .jpg .jpeg .png .gif .bmp .docx, .doc, .xls, .xlsx, .pdf 格式的文件"
        });
      },
      handleMaxSize(file) {
        this.loading = false;
        this.$Notice.warning({
          title: "文件大小过大",
          desc: "所选文件‘ " + file.name + " ’大小过大, 不得超过 10M."
        });
      },
      beforeUpload() {
        this.loading = true;
        return true;
      },
      handleSuccess(res, file) {
        console.log(res)
        this.loading = false;
        if (res.data.success) {
          file.url = res.data.url;
          this.currentValue = res.data;
          this.$emit("on-change", this.currentValue);
          this.$Message.success(res.message);
        } else {
          this.$Message.error(res.message);
        }
      },
      handleError(error, file, fileList) {
        this.loading = false;
        this.$Message.error(error.toString());
      },
      handleChange(v) {
        this.$emit("on-change", this.currentValue);
      },
      setCurrentValue(value) {
        if (value === this.currentValue) {
          return;
        }
        this.currentValue = value;
        this.$emit("on-change", this.currentValue);
      }
    },
    watch: {
      value(val) {
        this.setCurrentValue(val);
      }
    },
    created() {
      this.init();
    }
  };
</script>

<style lang="less">
  .upload {
    display: inline-block;
  }
</style>

