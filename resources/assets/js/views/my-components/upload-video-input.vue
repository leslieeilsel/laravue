<template>
  <div style="display: flex;">
    <Input
      :disabled="disabled"
      :maxlength="maxlength"
      :placeholder="placeholder"
      :readonly="readonly"
      :size="size"
      @on-change="handleChange"
      clearable
      v-model="currentValue"
    />
    <Upload
      :action="uploadFileUrl"
      :before-upload="beforeUpload"
      :format="['mp4']"
      :max-size="1024000"
      :on-error="handleError"
      :on-exceeded-size="handleMaxSize"
      :on-format-error="handleFormatError"
      :on-success="handleSuccess"
      :show-upload-list="false"
      accept=".mp4"
      class="upload"
      ref="uploadVideo"
    >
      <Button :disabled="disabled" :icon="icon" :loading="loading" :size="size">上传视频</Button>
    </Upload>
  </div>
</template>

<script>
  import {uploadFile} from "../../api/upload";

  export default {
    name: "uploadPicInput",
    props: {
      value: String,
      size: String,
      placeholder: {
        type: String,
        default: "可输入视频链接"
      },
      disabled: {
        type: Boolean,
        default: false
      },
      readonly: {
        type: Boolean,
        default: true
      },
      maxlength: Number,
      icon: {
        type: String,
        default: "ios-cloud-upload-outline"
      }
    },
    data() {
      return {
        currentValue: this.value,
        loading: false,
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
            " ’格式不正确, 请选择 .mp4 格式文件"
        });
      },
      handleMaxSize(file) {
        this.loading = false;
        this.$Notice.warning({
          title: "文件大小过大",
          desc: "所选文件大小过大, 不得超过 200M"
        });
      },
      beforeUpload() {
        this.loading = true;
        return true;
      },
      handleSuccess(res, file) {
        this.loading = false;
        if (res.data.success) {
          file.url = res.data.url;
          this.currentValue = res.data.url;
          this.$emit("input", this.currentValue);
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
        this.$emit("input", this.currentValue);
        this.$emit("on-change", this.currentValue);
      },
      setCurrentValue(value) {
        if (value === this.currentValue) {
          return;
        }
        this.currentValue = value;
        this.$emit("on-change", this.currentValue);
      },
      handleClearFiles() {
        this.currentValue = '';
        this.$refs.uploadVideo.clearFiles();
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
    margin-left: 10px;
  }
</style>

