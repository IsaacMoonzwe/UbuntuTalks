var slickopts = {
    slidesToShow: 4,
    slidesToScroll: 4,
    rows: 2, // Removes the linear order. Would expect card 5 to be on next row, not stacked in groups.
    responsive: [
      { breakpoint: 992,
        settings: {
          slidesToShow: 3
        }
      },
      { breakpoint: 776,
        settings: {
          slidesToShow: 1,
          rows: 1 // This doesn't appear to work in responsive (Mac/Chrome)
        }
      }]
  };
  
  $('.carousel').slick(slickopts);