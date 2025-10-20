async function findSlots(branchId, vehicleType, start, end) {
  const params = new URLSearchParams({ action:'available', branch_id:branchId, vehicle_type:vehicleType, start, end });
  const res = await fetch('/ocpms/public/search.php?' + params);
  const data = await res.json();
  return data.slots || [];
}
