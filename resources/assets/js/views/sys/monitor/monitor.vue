<template>
  <div>
    <Row>
      <Col>
        <Card>
          <Row>
            <div style="position:relative;">
              <iframe
                id="iframe"
                :src="go"
                frameborder="0"
                width="100%"
                height="700px"
                scrolling="auto"
              ></iframe>
              <Spin fix size="large" v-if="loading"></Spin>
            </div>
          </Row>
        </Card>
      </Col>
    </Row>
  </div>
</template>

<script>
  export default {
    name: "monitor",
    data() {
      return {
        loading: false,
        go: "",
        url: "",
        html: ""
      };
    },
    computed: {},
    methods: {
      initUrl() {
        console.log(this)
        let url = this.$route.meta.url;
        if (url !== null && url !== undefined) {
          this.url = url;
          this.go = url;
          // window.open(this.go);
          this.loading = true;
          let that = this;
          // 判断iframe是否加载完毕
          let iframe = document.getElementById("iframe");
          if (iframe.attachEvent) {
            iframe.attachEvent("onload", function () {
              //iframe加载完成后你需要进行的操作
              that.loading = false;
            });
          } else {
            iframe.onload = function () {
              //iframe加载完成后你需要进行的操作
              that.loading = false;
            };
          }
        }
      },
      handleGo() {
        let url = this.url;
        this.go = this.url;
      },
      handleOpen() {
        window.open(this.url);
      }
    },
    watch: {
      $route(to, from) {
        this.initUrl();
      }
    },
    mounted() {
      this.initUrl();
    }
  };
</script>

<style>
</style>
