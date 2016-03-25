var sequenceElement = document.getElementById("showcase-sequence");

// Place your Sequence options here to override defaults
// See: http://sequencejs.com/documentation/#options
var options = {
  animateCanvasDuration: 350,
  autoPlay: false,
  preloader: true,
  fadeStepWhenSkipped: false,
  moveActiveStepToTop: false
}

// Launch Sequence on the element, and with the options we specified above
var mySequence = sequence(sequenceElement, options);
