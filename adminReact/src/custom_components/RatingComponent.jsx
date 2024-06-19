import * as React from 'react';
import Rating from '@mui/material/Rating';
import Box from '@mui/material/Box';
import StarIcon from '@mui/icons-material/Star';

const labels = {

};

function getLabelText(value) {
  return `${value} Star${value !== 1 ? 's' : ''}`;
}

export default function HoverRating(probs) {
  const [value, setValue] = React.useState(probs.star_rate);


  return (
    <Box
      sx={{
        width: 200,
        display: 'flex',
        alignItems: 'center',
      }}
    >
      <Rating
        name="hover-feedback"
        value={value}
        precision={0.5}
        getLabelText={getLabelText}
        emptyIcon={<StarIcon style={{ opacity: 0.55 }} fontSize="inherit" />}
      />
      {value !== null && (
        <Box sx={{ ml: 2 }}>{labels[value]}</Box>
      )}
    </Box>
  );
}
