<template>
  <div>
    <test-video-player style="width: 100%;height: 100%;" :options="videoOptions"></test-video-player>
  </div>
</template>

<script>
  import 'videojs-flash';
  import TestVideoPlayer from "../my-components/test-video-player";
  
  export default {
    name: "dfgdfgdfg",
    components: {
        TestVideoPlayer
    },
    data() {
      return {
        videoOptions: {
          // playbackRates: [0.7, 1.0, 1.5, 2.0], //播放速度
          autoplay: false, //如果true,浏览器准备好时开始回放。
          muted: false, // 默认情况下将会消除任何音频。
          loop: false, // 导致视频一结束就重新开始。
          preload: 'auto', // 建议浏览器在<video>加载元素后是否应该开始下载视频数据。auto浏览器选择最佳行为,立即开始加载视频（如果浏览器支持）
          language: 'zh-CN',
          aspectRatio: '16:9', // 将播放器置于流畅模式，并在计算播放器的动态大小时使用该值。值应该代表一个比例 - 用冒号分隔的两个数字（例如"16:9"或"4:3"）
          fluid: true, // 当true时，Video.js player将拥有流体大小。换句话说，它将按比例缩放以适应其容器。
          controls: true,
          sources: [
            {
              src:
                "rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022723/0/MAIN/TCP",
              type: "rtmp/mp4"
            }
          ],
          // poster: "./fx1.png", //你的封面地址
          // width: document.documentElement.clientWidth,
          // height: 100,
          width:200,
          height:200,
        }
      };
    }
  };
  </script>
<style lang="stylus" scoped>
.vjs-paused .vjs-big-play-button,
.vjs-paused.vjs-has-started .vjs-big-play-button {
    display: block;
}
.video-js.vjs-playing .vjs-tech {
    pointer-events: auto;
}
</style>